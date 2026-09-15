<?php

namespace Tests\Feature;

use App\Models\Post;
use Eshlink\Cms\Media\MediaService;
use Eshlink\Cms\Models\Media;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * A photograph, from the moment it is added to the moment a visitor sees it.
 *
 * This is the whole of the media feature stated as one journey, because that
 * is the only way it was ever going to be caught. Every piece of it worked on
 * its own: the upload stored the file, the row was written, the thumbnail was
 * generated, the library rendered an `<img>`. And every one of those images was
 * broken, because the URL in the `src` belonged to a private disk and the route
 * behind it refused every unsigned request. Nothing failed. The picture simply
 * never appeared.
 *
 * So the assertion that matters is not "a URL was produced". It is: fetch that
 * URL, the way a browser would, with no session, on the public host, and get
 * the bytes.
 */
class PhotoOnAPublishedPageTest extends TestCase
{
    use LazilyRefreshDatabase;

    /**
     * The public host, named rather than left to the default.
     *
     * "On the public host" is in this file's own description, and it was the
     * one thing it did not do: the suite's default host is `marina.localhost`,
     * which resolves as **preview**, where `ForceNoIndex` clamps every response
     * to `no-store` and `asset()` builds preview addresses. So the immutable
     * caching this asserts was being contradicted by a header that has nothing
     * to do with media, and the absolute URLs came out pointing at a host no
     * visitor uses.
     */
    private const PRODUCTION = 'http://marinanewyorkcity.com';

    #[Test]
    public function a_photograph_added_to_the_library_can_be_fetched_by_anybody(): void
    {
        $media = $this->addPhotograph();
        $url = app(MediaService::class)->url($media);

        // Root-relative, so one stored value works on the admin host and on
        // the public host. An absolute URL here would pin the photograph to
        // whichever host happened to be answering when it was chosen.
        $this->assertStringStartsWith('/cms-media/', $url);

        $response = $this->get(self::PRODUCTION.$url);

        $response->assertOk();
        $this->assertStringStartsWith('image/', (string) $response->headers->get('Content-Type'));

        // The name is the checksum, so these bytes cannot change without the
        // URL changing, and a year of caching is a fact rather than a hope.
        $this->assertStringContainsString('immutable', (string) $response->headers->get('Cache-Control'));
    }

    #[Test]
    public function the_thumbnail_the_library_grid_draws_is_fetchable_too(): void
    {
        $media = $this->addPhotograph();

        $this->get(self::PRODUCTION.app(MediaService::class)->url($media, 'thumb'))
            ->assertOk();
    }

    #[Test]
    public function a_photograph_chosen_as_a_cover_shows_up_on_the_published_page(): void
    {
        $media = $this->addPhotograph();
        $url = app(MediaService::class)->url($media);

        // What the picker writes into a `storesPath()` image field: the same
        // public path, straight into the column the site has always read.
        $post = Post::factory()->create([
            'cover_path' => $url,
            'published_at' => now()->subDay(),
        ]);

        $page = $this->get(self::PRODUCTION.'/post/'.$post->slug);

        $page->assertOk();

        // `asset()` resolves the stored root-relative path against the host
        // answering, so the page carries the absolute address and the social
        // card meta carries the production one. Both end in the same path.
        // The page's own <img> follows the host that answered, scheme and all.
        // Only the literal 127.0.0.1:8000 was wrong here: that was a fact about
        // one developer's machine, and the app has since moved.
        $page->assertSee('<img src="'.self::PRODUCTION.$url.'"', false);
        // The social card does NOT follow the answering host. It is canonical
        // and https, because the card is fetched by Facebook and Slack long
        // after the response is gone, from whatever address is in the markup,
        // and that address has to be the real one over TLS. So this stays the
        // literal it always was, and the difference between the two lines is
        // the point rather than an inconsistency.
        $page->assertSee('og:image" content="https://marinanewyorkcity.com'.$url.'"', false);

        // And the address on the page is one a visitor can actually GET.
        $this->get(self::PRODUCTION.$url)->assertOk();
    }

    #[Test]
    public function a_picture_the_site_shipped_with_still_renders_exactly_as_before(): void
    {
        // The reason `storesPath()` exists. Marina's live rows hold paths under
        // `public/`, written long before this package did, and adopting the
        // photo library must not disturb a single one of them.
        $post = Post::factory()->create([
            'cover_path' => 'media/posts/example.jpg',
            'published_at' => now()->subDay(),
        ]);

        $this->get(self::PRODUCTION.'/post/'.$post->slug)
            ->assertOk()
            ->assertSee('media/posts/example.jpg', false);
    }

    #[Test]
    public function nothing_outside_the_media_folder_can_be_asked_for(): void
    {
        // The route reads one disk under one prefix. It is not a file server
        // with a content-addressed skin on.
        $this->get(self::PRODUCTION.'/cms-media/../../.env')->assertNotFound();
        $this->get(self::PRODUCTION.'/cms-media/framework/sessions/anything')->assertNotFound();
    }

    /**
     * A photograph on a disk that cannot address itself.
     *
     * The disk is pinned rather than inherited, and that is the point. What is
     * under test is the package's own delivery route — the answer for every
     * disk with no public address of its own, and the place the original bug
     * lived, where the URL was produced and the route behind it then refused
     * every unsigned request. Inheriting `cms.media.disk` made that a lottery:
     * locally it resolves to `public`, which *can* address itself, so the URL
     * came out as `/storage/...`, there is no `public/storage` symlink in a
     * checkout and no route behind that path either, and three tests failed on
     * an environment rather than on the code.
     *
     * `local` has no `url` and is not `visibility: public`, so
     * `MediaService::isPubliclyAddressable()` says no and the proxy answers.
     *
     * **What this deliberately does not cover:** production sets
     * `CMS_MEDIA_DISK` to S3, which addresses itself, so the bytes there are
     * fetched from the bucket and never pass through this route at all.
     */
    private function addPhotograph(): Media
    {
        config(['cms.media.disk' => 'local']);

        Storage::fake('local');

        return app(MediaService::class)->store(
            $this->jpeg(),
            'A yellow circle on a blue field.',
            ['filename' => 'circle.jpg'],
        );
    }

    /**
     * A real JPEG, because the pipeline reads the bytes with finfo and refuses
     * anything whose type is only claimed by its name.
     */
    private function jpeg(): string
    {
        $image = imagecreatetruecolor(600, 400);
        imagefilledrectangle($image, 0, 0, 600, 400, imagecolorallocate($image, 30, 90, 160));
        imagefilledellipse($image, 300, 200, 220, 220, imagecolorallocate($image, 255, 220, 60));

        ob_start();
        imagejpeg($image, null, 90);

        return (string) ob_get_clean();
    }
}
