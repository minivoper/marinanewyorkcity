<?php

namespace App\Cms\Types;

use Eshlink\Cms\Schema\Fields\Text;
use Eshlink\Cms\Schema\Fields\Textarea;
use Eshlink\Cms\Schema\Schema;

/**
 * `/travel-usa` — a full-bleed looping video behind one heading.
 */
class TravelUsaType extends PageSingleton
{
    public function key(): string
    {
        return 'travel_usa';
    }

    public function label(): string
    {
        return 'Travel USA page';
    }

    public function schema(): Schema
    {
        return Schema::make([
            Text::make('heading')->required()->max(120),
            Text::make('video_poster')->required()->max(255)
                ->help('Path under public/ to the still shown while the video loads.'),
            Text::make('video_path')->required()->max(255)
                ->help('Path under public/ to the looping MP4.'),
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
            'heading' => 'TRAVEL USA',
            'video_poster' => 'media/travel/travel-usa-poster.jpg',
            'video_path' => 'media/travel/travel-usa-1080p.mp4',
            'seo_title' => 'TRAVEL USA | marina.newyorkcity',
            'seo_description' => 'Cinematic travel stories from across the USA by Marina Kapler.',
        ];
    }
}
