<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class HomeTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_home_renders_new_york_content(): void
    {
        $this->seed();

        $this->get('/')
            ->assertOk()
            ->assertSeeText('Marina New York City');
    }
}
