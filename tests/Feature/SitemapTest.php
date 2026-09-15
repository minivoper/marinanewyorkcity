<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class SitemapTest extends TestCase
{
    use LazilyRefreshDatabase;

    /**
     * The sitemap is generated on production hosts and 404s everywhere else,
     * so this has to name the host. The suite's default is `marina.localhost`,
     * which resolves as a preview host.
     */
    private const PRODUCTION = 'http://marinanewyorkcity.com';

    public function test_sitemap_contains_post_and_event_urls(): void
    {
        $this->seed();

        $this->get(self::PRODUCTION.'/sitemap.xml')
            ->assertOk()
            ->assertSee('/post/', false)
            ->assertSee('/event-details/', false);
    }
}
