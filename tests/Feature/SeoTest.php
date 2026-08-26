<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class SeoTest extends TestCase
{
    use LazilyRefreshDatabase;

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
        $this->get('/robots.txt')
            ->assertOk()
            ->assertSeeText('User-agent: GPTBot')
            ->assertSeeText('Allow: /');
    }

    public function test_llms_identifies_marina_kapler(): void
    {
        $this->get('/llms.txt')
            ->assertOk()
            ->assertSeeText('Marina Kapler');
    }

    public function test_feeds_render_seeded_posts(): void
    {
        $this->seed();

        $this->get('/feed.xml')
            ->assertOk()
            ->assertSeeText('How to Create a Cinematic Video with an iPhone');

        $this->get('/feed.json')
            ->assertOk()
            ->assertJsonPath('version', 'https://jsonfeed.org/version/1.1');
    }
}
