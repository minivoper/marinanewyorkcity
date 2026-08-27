<?php

namespace Tests\Feature;

use Eshlink\Cms\Http\Middleware\BlockCrawlers;
use Eshlink\Cms\Http\Middleware\ForceNoIndex;
use Eshlink\Cms\Http\Middleware\PreviewGate;
use Eshlink\Cms\Http\Middleware\PreviewToLiveRedirect;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * The middleware stack registered in bootstrap/app.php.
 *
 * `HostModeTest` covers what the *rendered page* says on each host — the
 * canonical tag, the sitemap, the JSON-LD. This covers the layer underneath it:
 * the response headers, on responses no page ever rendered.
 *
 * The case that matters is the 404. Every crawler that ignores robots.txt finds
 * dead paths, and a 404 without `X-Robots-Tag` is exactly the response it keeps
 * — which is how a preview host that renders no canonical tags anywhere still
 * ends up in an index. Nothing matched a route, so nothing in a middleware
 * *group* ran; only a global middleware wraps routing itself, which is why
 * `CmsServiceProvider::forceNoIndexGlobally()` exists and why it is called
 * rather than the `cms.no-index` alias being appended to `web`.
 *
 * The other half is the one that must never fail: the production host answers
 * exactly as it did before any of this was registered.
 */
class NoIndexStackTest extends TestCase
{
    use LazilyRefreshDatabase;

    private const PREVIEW = 'http://marina.eshlink.com';

    private const PRODUCTION = 'http://marinanewyorkcity.com';

    /**
     * Headers a production response must not acquire. `Cache-Control` is not
     * in the list because Laravel sets one of its own on every response; it is
     * asserted separately, by value.
     *
     * @var array<int, string>
     */
    private const PREVIEW_ONLY_HEADERS = [
        'x-robots-tag',
        'referrer-policy',
        'pragma',
        'expires',
    ];

    public function test_the_preview_host_serves_noindex_on_a_page_that_exists(): void
    {
        $response = $this->get(self::PREVIEW.'/shop')->assertOk();

        $this->assertContains(
            ForceNoIndex::DIRECTIVES,
            $response->headers->all('x-robots-tag'),
        );
    }

    public function test_a_404_on_the_preview_host_carries_the_headers_too(): void
    {
        $response = $this->get(self::PREVIEW.'/a-path-that-was-never-a-page');

        $response->assertNotFound();

        $robots = $response->headers->all('x-robots-tag');

        // The generic directive, and the named crawlers a per-bot line is the
        // only way to refuse — the AI training bots especially.
        $this->assertContains(ForceNoIndex::DIRECTIVES, $robots);
        $this->assertContains('googlebot: '.ForceNoIndex::DIRECTIVES, $robots);
        $this->assertContains('gptbot: '.ForceNoIndex::DIRECTIVES, $robots);
        $this->assertContains('claudebot: '.ForceNoIndex::DIRECTIVES, $robots);
        $this->assertContains('google-extended: '.ForceNoIndex::DIRECTIVES, $robots);

        // A preview link pasted into a chat must not leak the host to whatever
        // gets clicked next, and no shared cache may keep the page.
        $this->assertSame('no-referrer', $response->headers->get('Referrer-Policy'));
        $this->assertStringContainsString('no-store', (string) $response->headers->get('Cache-Control'));
    }

    public function test_a_500_on_the_preview_host_carries_the_headers_too(): void
    {
        Route::get('/_test/explode', function (): void {
            throw new \RuntimeException('Boom.');
        })->middleware('web');

        $response = $this->get(self::PREVIEW.'/_test/explode');

        $response->assertServerError();

        $this->assertContains(
            ForceNoIndex::DIRECTIVES,
            $response->headers->all('x-robots-tag'),
        );
    }

    public function test_a_named_crawler_is_refused_the_preview_host(): void
    {
        $this->get(self::PREVIEW.'/shop', ['User-Agent' => 'Mozilla/5.0 (compatible; GPTBot/1.2)'])
            ->assertForbidden();
    }

    public function test_the_health_check_is_never_filtered_by_user_agent(): void
    {
        // A liveness probe must not depend on a UA allowlist, and an MCP client
        // is the point rather than an attack.
        $this->get(self::PREVIEW.'/up', ['User-Agent' => 'Mozilla/5.0 (compatible; GPTBot/1.2)'])
            ->assertOk();
    }

    public function test_the_production_host_is_untouched_by_any_of_it(): void
    {
        $this->seed();

        $response = $this->get(self::PRODUCTION.'/shop')->assertOk();

        foreach (self::PREVIEW_ONLY_HEADERS as $header) {
            $this->assertSame(
                [],
                $response->headers->all($header),
                "The production host acquired a {$header} header it did not have before.",
            );
        }

        // Not `no-store`. Production is where the whole SEO surface exists to
        // be found, and a page a CDN refuses to hold is a slower page for no
        // reason at all.
        $this->assertStringNotContainsString(
            'no-store',
            (string) $response->headers->get('Cache-Control'),
        );

        // The crawler denylist has no opinion on a production host either.
        $this->get(self::PRODUCTION.'/shop', ['User-Agent' => 'Mozilla/5.0 (compatible; Googlebot/2.1)'])
            ->assertOk();
    }

    public function test_a_production_response_is_byte_identical_with_the_stack_registered(): void
    {
        $this->seed();

        $before = $this->get(self::PRODUCTION.'/shop')->assertOk();

        // The same request with the CMS middleware explicitly stripped. If the
        // stack changed a single byte of what the public sees, these differ.
        $after = $this->withoutMiddleware([
            ForceNoIndex::class,
            BlockCrawlers::class,
            PreviewGate::class,
            PreviewToLiveRedirect::class,
        ])->get(self::PRODUCTION.'/shop')->assertOk();

        $this->assertSame($after->getContent(), $before->getContent());
        $this->assertSame($after->getStatusCode(), $before->getStatusCode());
    }
}
