<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class WixPagesTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_news_and_press_renders_news_posts(): void
    {
        $this->seed();

        $this->get('/news-and-press')
            ->assertOk()
            ->assertSeeText('NEWS AND PRESS')
            ->assertSeeText('Lincoln Center Begins Major West Side Transformation in New York City');
    }

    public function test_shop_renders_digital_products_and_merch(): void
    {
        $this->get('/shop')
            ->assertOk()
            ->assertSeeText('Digital products')
            ->assertSee('https://links.marinanewyorkcity.com/digitalproducts');
    }

    public function test_contact_renders_form(): void
    {
        $this->get('/contact')
            ->assertOk()
            ->assertSeeText('CONTACT US')
            ->assertSee('name="contacting_for"', false);
    }

    public function test_contact_submission_flashes_success(): void
    {
        $this->post('/contact', [
            'contacting_for' => 'Other',
            'name' => 'Test Person',
            'email' => 'test@example.com',
            'message' => 'Hello from the test suite.',
        ])
            ->assertRedirect(route('contact.show'))
            ->assertSessionHas('status');
    }

    public function test_wix_slugs_redirect_to_pretty_urls(): void
    {
        $this->get('/blank')->assertMovedPermanently()->assertRedirect('/news-and-press');
        $this->get('/blank-4')->assertMovedPermanently()->assertRedirect('/shop');
        $this->get('/blank-6')->assertMovedPermanently()->assertRedirect('/free-events');
    }

    public function test_free_events_renders_seeded_events(): void
    {
        $this->seed();

        $this->get('/free-events')
            ->assertOk()
            ->assertSeeText('FREE EVENTS')
            ->assertSeeText('NYC Pride March');
    }

    public function test_travel_usa_merch_and_accessibility_render(): void
    {
        $this->get('/travel-usa')->assertOk()->assertSeeText('TRAVEL USA');
        $this->get('/merch')->assertOk()->assertSeeText('Scented Soy Candle');
        $this->get('/accessibility-statement')->assertOk()->assertSeeText('ACCESSIBILITY STATEMENT');
    }
}
