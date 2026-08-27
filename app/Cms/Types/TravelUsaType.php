<?php

namespace App\Cms\Types;

use Eshlink\Cms\Schema\Fields\Image;
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
        return 'Travel USA';
    }

    public function blurb(): ?string
    {
        return 'Your travel guides across the country.';
    }

    public function schema(): Schema
    {
        return Schema::make([
            Text::make('heading')->required()->max(120),
            Image::make('video_poster')->storesPath()->required()->max(255)
                ->withLabel('Still picture, shown while the film loads')
                ->help('Choose one from your photos. The picture that is there now still works, so you can leave it as it is.'),
            Text::make('video_path')->required()->max(255)
                ->withLabel('The looping film')
                ->help('The film that plays behind this section. Films are not kept in your photo library, so this one is set by the site.'),
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

    /**
     * @param  array<string, mixed>  $entry
     */
    public function publicPath(array $entry): ?string
    {
        return '/travel-usa';
    }
}
