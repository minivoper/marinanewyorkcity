<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class EventTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_event_list_renders_seeded_events(): void
    {
        $this->seed();

        $this->get('/event-list')
            ->assertOk()
            ->assertSeeText('NYC Pride March');
    }

    public function test_event_details_render_event_slug(): void
    {
        $this->seed();

        $this->get('/event-details/nyc-pride-march')
            ->assertOk()
            ->assertSeeText('NYC Pride March');
    }

    public function test_event_details_render_occurrence_slug(): void
    {
        $this->seed();

        $this->get('/event-details/bryant-park-movie-nights-2026-07-13')
            ->assertOk()
            ->assertSeeText('Bryant Park Movie Nights')
            ->assertSeeText('Selected date');
    }

    public function test_search_renders_bryant_park_results(): void
    {
        $this->seed();

        $this->get('/search?q=bryant')
            ->assertOk()
            ->assertSeeText('Bryant Park Movie Nights');
    }
}
