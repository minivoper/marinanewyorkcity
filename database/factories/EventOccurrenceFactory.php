<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\EventOccurrence;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EventOccurrence>
 */
class EventOccurrenceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startsAt = fake()->dateTimeBetween('+1 day', '+1 month');

        return [
            'event_id' => Event::factory(),
            'starts_at' => $startsAt,
            'ends_at' => (clone $startsAt)->modify('+2 hours'),
            'occurrence_slug' => fake()->unique()->slug(),
        ];
    }
}
