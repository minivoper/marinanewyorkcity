<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class PostTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_blog_renders_seeded_cinematic_iphone_story(): void
    {
        $this->seed();

        $this->get('/blog')
            ->assertOk()
            ->assertSeeText('How to Create a Cinematic Video with an iPhone');
    }

    public function test_post_route_renders_seeded_cinematic_iphone_story(): void
    {
        $this->seed();

        $this->get('/post/how-to-create-a-cinematic-video-with-an-iphone')
            ->assertOk()
            ->assertSeeText('How to Create a Cinematic Video with an iPhone');
    }
}
