<?php

namespace Database\Factories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => Post::TYPE_NEWS,
            'slug' => fake()->unique()->slug(),
            'title' => fake()->sentence(),
            'excerpt' => fake()->paragraph(),
            'body' => '<p>'.fake()->paragraphs(3, true).'</p>',
            'published_at' => now()->subDay(),
            'read_minutes' => 3,
            'meta_title' => fake()->sentence(),
            'meta_description' => fake()->paragraph(),
            'geo_summary' => 'Current New York City news and culture summarized clearly for local readers.',
            'schema_type' => 'NewsArticle',
        ];
    }
}
