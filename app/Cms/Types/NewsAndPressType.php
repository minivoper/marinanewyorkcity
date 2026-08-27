<?php

namespace App\Cms\Types;

use Eshlink\Cms\Schema\Fields\Text;
use Eshlink\Cms\Schema\Fields\Textarea;
use Eshlink\Cms\Schema\Schema;

/**
 * `/news-and-press` — the intro around the paginated list of news stories.
 *
 * The stories are rows edited through {@see PostType}; this type owns only the
 * page's own words.
 */
class NewsAndPressType extends PageSingleton
{
    public function key(): string
    {
        return 'news_and_press';
    }

    public function label(): string
    {
        return 'News and Press page';
    }

    public function schema(): Schema
    {
        return Schema::make([
            Text::make('heading')->required()->max(120),
            Text::make('empty_message')->required()->max(255)
                ->help('Shown when no stories are published.'),
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
            'heading' => 'NEWS AND PRESS',
            'empty_message' => 'No published stories found.',
            'seo_title' => 'NEWS AND PRESS | marina.newyorkcity',
            'seo_description' => 'New York City news and press releases by Marina Kapler.',
        ];
    }
}
