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

    #[Test]
    public function a_photograph_added_to_the_library_can_be_fetched_by_anybody(): void
    {
        $media = $this->addPhotograph();
        $url = app(MediaService::class)->url($media);

        // Root-relative, so one stored value works on the admin host and on
        // the public host. An absolute URL here would pin the photograph to
        // whichever host happened to be answering when it was chosen.
        $this->assertStringStartsWith('/cms-media/', $url);

        $response = $this->get($url);

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

        $this->get(app(MediaService::class)->url($media, 'thumb'))
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

        $page = $this->get('/post/'.$post->slug);

        $page->assertOk();

        // `asset()` resolves the stored root-relative path against the host
        // answering, so the page carries the absolute address and the social
        // card meta carries the production one. Both end in the same path.
        $page->assertSee('<img src="http://127.0.0.1:8000'.$url.'"', false);
        $page->assertSee('og:image" content="https://marinanewyorkcity.com'.$url.'"', false);

        // And the address on the page is one a visitor can actually GET.
        $this->get($url)->assertOk();
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

        $this->get('/post/'.$post->slug)
            ->assertOk()
            ->assertSee('media/posts/example.jpg', false);
    }

    #[Test]
    public function nothing_outside_the_media_folder_can_be_asked_for(): void
    {
        // The route reads one disk under one prefix. It is not a file server
        // with a content-addressed skin on.
        $this->get('/cms-media/../../.env')->assertNotFound();
        $this->get('/cms-media/framework/sessions/anything')->assertNotFound();
    }

    private function addPhotograph(): Media
    {
        Storage::fake(config('cms.media.disk'));

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
