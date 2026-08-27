<?php

namespace App\Cms\Types;

use Eshlink\Cms\Schema\Fields\Slug;
use Eshlink\Cms\Schema\Fields\Text;
use Eshlink\Cms\Schema\Fields\Textarea;
use Eshlink\Cms\Schema\Schema;

/**
 * `/how-i-create` — a heading over one featured story.
 *
 * Which story that is used to be a slug hardcoded in `PageController`, which
 * meant changing the feature was a deploy. It is a field now, and the
 * controller still resolves it through `Post::published()`, so an unpublished
 * or misspelled slug renders the page without the card rather than 404ing.
 */
class HowICreateType extends PageSingleton
{
    public function key(): string
    {
        return 'how_i_create';
    }

    public function label(): string
    {
        return 'How I Create page';
    }

    public function schema(): Schema
    {
        return Schema::make([
            Text::make('heading')->required()->max(120),
            Slug::make('featured_post_slug')->required()->max(255)
                ->help('The address of the story to feature, e.g. how-to-create-a-cinematic-video-with-an-iphone.'),
            Text::make('seo_title')->required()->max(255),
            Textarea::make('seo_description')->required()->max(320)->rows(3),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function defaults(): array
    {
        return [
            'heading' => 'HOW I CREATE',
            'featured_post_slug' => 'how-to-create-a-cinematic-video-with-an-iphone',
            'seo_title' => 'HOW I CREATE | marina.newyorkcity',
            'seo_description' => 'How Marina Kapler creates cinematic New York City content with an iPhone.',
        ];
    }
}
