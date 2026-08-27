<?php

namespace App\Cms\Types;

use Eshlink\Cms\Schema\Fields\Text;
use Eshlink\Cms\Schema\Fields\Textarea;
use Eshlink\Cms\Schema\Schema;

/**
 * `/free-events` — the chrome around the event list.
 *
 * The events themselves are rows, edited through {@see EventType}; what lives
 * here is the heading, the link label on each row, and what the page says when
 * there is nothing on.
 */
class FreeEventsType extends PageSingleton
{
    public function key(): string
    {
        return 'free_events';
    }

    public function label(): string
    {
        return 'Free Events';
    }

    public function blurb(): ?string
    {
        return 'The free things happening around the city.';
    }

    public function schema(): Schema
    {
        return Schema::make([
            Text::make('heading')->required()->max(120),
            Text::make('cta_label')->required()->max(60),
            Text::make('empty_message')->required()->max(255)
                ->help('Shown when no events are listed.'),
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
            'heading' => 'FREE EVENTS',
            'cta_label' => 'More info',
            'empty_message' => 'No events found.',
            'seo_title' => 'FREE EVENTS | marina.newyorkcity',
            'seo_description' => 'Free New York City events selected by Marina Kapler.',
        ];
    }

    /**
     * @param  array<string, mixed>  $entry
     */
    public function publicPath(array $entry): ?string
    {
        return '/free-events';
    }
}
