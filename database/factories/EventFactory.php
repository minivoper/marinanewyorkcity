<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'slug' => fake()->unique()->slug(),
            'title' => fake()->sentence(),
            'excerpt' => fake()->paragraph(),
            'body' => '<p>'.fake()->paragraphs(2, true).'</p>',
            'venue_name' => fake()->company(),
            'venue_address' => fake()->streetAddress().', New York, NY',
            'timezone' => 'America/New_York',
            'meta_title' => fake()->sentence(),
            'meta_description' => fake()->paragraph(),
            'geo_summary' => 'An event in New York City.',
        ];
    }
}
