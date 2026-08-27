<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

/**
 * The page a wrong link lands on.
 *
 * Landing here is ordinary rather than exceptional on this site: the legal
 * pages still carry addresses written on the old Wix site, a renamed story
 * keeps its old address in somebody's bookmarks, and an Instagram caption
 * outlives the post it points at. Laravel's own not-found page is a white
 * sheet with no header, no navigation and nothing that says whose site this
 * is — a page whose only exit is the back button.
 *
 * So the assertions here are about escape routes rather than about wording:
 * the site's own frame, and somewhere to go next. The status code stays 404,
 * because a branded page that answered 200 would teach every crawler that
 * every dead link is a real one.
 */
class PublicNotFoundTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_a_dead_link_is_answered_by_marinas_own_page(): void
    {
        $response = $this->get('/no-such-page-at-all')->assertNotFound();

        $response->assertSeeText('PAGE NOT FOUND');

        // Her frame, not a bare document: the header, the navigation and the
        // footer the rest of the site is wearing.
        $response->assertSee('class="site-header', false);
        $response->assertSee('id="primary-nav"', false);
        $response->assertSee('class="site-footer"', false);

        // And a way onward, named rather than implied.
        $response->assertSeeText('Back to the home page');
        $response->assertSee('href="'.route('home').'"', false);
        $response->assertSee('href="'.route('posts.index').'"', false);
        $response->assertSee('href="'.route('events.index').'"', false);

        // Laravel's own page, which is what was there before.
        $response->assertDontSee('<title>Not Found</title>', false);
    }

    public function test_the_not_found_page_says_what_it_is_to_a_crawler_too(): void
    {
        $response = $this->get('/no-such-page-at-all')->assertNotFound();

        $response->assertSee('Page Not Found | marina.newyorkcity', false);
    }

    /**
     * A real page is still a real page. This guards the one way a custom 404
     * view goes wrong: catching more than it was meant to.
     */
    public function test_every_real_page_still_answers_itself(): void
    {
        $this->seed();

        foreach (['/', '/blog', '/shop', '/contact', '/about', '/event-list'] as $path) {
            $this->get($path)->assertOk()->assertDontSeeText('PAGE NOT FOUND');
        }
    }
}
