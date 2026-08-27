<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

/**
 * The discovery posture, per host.
 *
 * `marina.eshlink.com` is reachable by anyone who has the link and
 * undiscoverable to everyone else. The control that matters here is the one
 * that survives a crawler ignoring robots.txt: there is no canonical tag, no
 * og:url and no JSON-LD `url` pointing at the real domain, and the machine
 * readable index of the site — sitemap, feeds, llms.txt — is not served at all.
 */
class HostModeTest extends TestCase
{
    use LazilyRefreshDatabase;

    private const PREVIEW = 'http://marina.eshlink.com';

    protected function setUp(): void
    {
        parent::setUp();

        config(['cms.site_domains' => [
            'marinanewyorkcity.com' => 'production',
            'localhost' => 'production',
            'marina.eshlink.com' => 'preview',
        ]]);
    }

    public function test_the_production_host_publishes_its_canonical_url(): void
    {
        $this->get('http://localhost/shop')
            ->assertOk()
            ->assertSee('<link rel="canonical" href="https://marinanewyorkcity.com/shop">', false)
            ->assertSee('<meta property="og:url" content="https://marinanewyorkcity.com/shop">', false);
    }

    public function test_the_preview_host_publishes_no_canonical_url_at_all(): void
    {
        $response = $this->get(self::PREVIEW.'/shop')->assertOk();

        $this->assertSame(0, substr_count($response->getContent(), 'rel="canonical"'));
        $this->assertSame(0, substr_count($response->getContent(), 'og:url'));
        $this->assertSame(0, substr_count($response->getContent(), 'hreflang'));
    }

    public function test_the_preview_host_leaves_the_real_domain_out_of_json_ld_urls(): void
    {
        $this->seed();

        $production = $this->get('http://localhost/post/how-to-create-a-cinematic-video-with-an-iphone')->assertOk();
        $this->assertStringContainsString('"url":"https://marinanewyorkcity.com/post/', $production->getContent());

        $preview = $this->get(self::PREVIEW.'/post/how-to-create-a-cinematic-video-with-an-iphone')->assertOk();
        $this->assertStringNotContainsString('"url":"https://marinanewyorkcity.com', $preview->getContent());
    }

    public function test_the_preview_host_serves_no_sitemap_feeds_or_llms_txt(): void
    {
        $this->seed();

        $this->get(self::PREVIEW.'/sitemap.xml')->assertNotFound();
        $this->get(self::PREVIEW.'/llms.txt')->assertNotFound();
        $this->get(self::PREVIEW.'/ai.txt')->assertNotFound();
        $this->get(self::PREVIEW.'/feed.xml')->assertNotFound();
        $this->get(self::PREVIEW.'/feed.json')->assertNotFound();
    }

    public function test_the_preview_host_answers_robots_txt_with_a_refusal(): void
    {
        $this->get(self::PREVIEW.'/robots.txt')
            ->assertOk()
            ->assertSeeText('Disallow: /')
            ->assertDontSeeText('Sitemap:');
    }

    public function test_the_production_host_still_serves_the_whole_seo_surface(): void
    {
        $this->seed();

        $this->get('http://localhost/sitemap.xml')->assertOk();
        $this->get('http://localhost/llms.txt')->assertOk()->assertSeeText('Marina Kapler');
        $this->get('http://localhost/feed.xml')->assertOk();
        $this->get('http://localhost/feed.json')->assertOk();
        $this->get('http://localhost/robots.txt')->assertOk()->assertSeeText('User-agent: GPTBot');
    }

    /**
     * An unrecognised Host header is the case a proxy misconfiguration or a
     * stray CI request produces. Falling back to the undiscoverable posture is
     * the failure that cannot leak the site.
     */
    public function test_an_unknown_host_falls_back_to_the_undiscoverable_posture(): void
    {
        $this->get('http://something-nobody-declared.test/shop')
            ->assertOk()
            ->assertDontSee('rel="canonical"', false);
    }
}
