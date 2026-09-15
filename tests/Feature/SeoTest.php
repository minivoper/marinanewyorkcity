<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class SeoTest extends TestCase
{
    use LazilyRefreshDatabase;

    /**
     * The SEO surfaces exist on the production host and nowhere else.
     *
     * That is the design, not an accident: `robots.txt` answers `Disallow: /`
     * on a preview host and `sitemap.xml`, `llms.txt` and the feeds 404 there,
     * so that a hidden host cannot be crawled into an index. The suite's
     * default host is `marina.localhost`, which resolves as **preview** — so a
     * test that asks for these without naming a host is asking the one host
     * that is supposed to refuse, and was failing for exactly that reason.
     *
     * Same spelling as `NoIndexStackTest`, which drives its hosts the same way.
     */
    private const PRODUCTION = 'http://marinanewyorkcity.com';

    public function test_privacy_policy_renders_site_domain(): void
    {
        $this->seed();

        $this->get('/privacy-policy')
            ->assertOk()
            ->assertSeeText('marinanewyorkcity.com');
    }

    public function test_terms_and_conditions_render(): void
    {
        $this->seed();

        $this->get('/terms-and-conditions')
            ->assertOk()
            ->assertSeeText('Effective Date: May 27, 2026');
    }

    public function test_robots_allows_gptbot(): void
    {
        $this->get(self::PRODUCTION.'/robots.txt')
            ->assertOk()
            ->assertSeeText('User-agent: GPTBot')
            ->assertSeeText('Allow: /');

        // And the preview host still refuses, which is the half of this that
        // would be silently lost if the production host were the only one
        // asserted.
        $this->get('http://marina.eshlink.com/robots.txt')
            ->assertOk()
            ->assertSeeText('Disallow: /');
    }

    public function test_llms_identifies_marina_kapler(): void
    {
        $this->get(self::PRODUCTION.'/llms.txt')
            ->assertOk()
            ->assertSeeText('Marina Kapler');
    }

    public function test_feeds_render_seeded_posts(): void
    {
        $this->seed();

        $this->get(self::PRODUCTION.'/feed.xml')
            ->assertOk()
            ->assertSeeText('How to Create a Cinematic Video with an iPhone');

        $this->get(self::PRODUCTION.'/feed.json')
            ->assertOk()
            ->assertJsonPath('version', 'https://jsonfeed.org/version/1.1');
    }
}
