<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class SitemapTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_sitemap_contains_post_and_event_urls(): void
    {
        $this->seed();

        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertSee('/post/', false)
            ->assertSee('/event-details/', false);
    }
}
