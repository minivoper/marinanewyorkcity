<?php

namespace Database\Factories;

use App\Models\Page;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Page>
 */
class PageFactory extends Factory
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
            'body' => '<p>'.fake()->paragraphs(3, true).'</p>',
            'meta_title' => fake()->sentence(),
            'meta_description' => fake()->paragraph(),
        ];
    }
}
