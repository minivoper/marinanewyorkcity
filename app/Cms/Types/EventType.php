<?php

namespace App\Cms\Types;

use App\Models\Event;
use Eshlink\Cms\Contracts\ContentSource;
use Eshlink\Cms\Rules\SafeHtml;
use Eshlink\Cms\Schema\Fields\DateTime;
use Eshlink\Cms\Schema\Fields\Repeater;
use Eshlink\Cms\Schema\Fields\RichText;
use Eshlink\Cms\Schema\Fields\Slug;
use Eshlink\Cms\Schema\Fields\Text;
use Eshlink\Cms\Schema\Fields\Textarea;
use Eshlink\Cms\Schema\Schema;
use Eshlink\Cms\Sources\ModelSource;
use Eshlink\Cms\Sources\RelationMap;

/**
 * Events, with their dates edited as a repeater over the `event_occurrences`
 * relation Marina's views already iterate.
 *
 * Occurrences are matched to child rows by `id`, not by position: an event
 * such as Bryant Park Movie Nights has twelve dates, each with its own
 * `occurrence_slug` that `/event-details/{slug}` resolves against and that the
 * sitemap lists. Positional matching would renumber every date after a
 * cancellation and silently move a dozen public URLs onto the wrong evenings.
 *
 * `ModelSource` syncs the relation row by row rather than truncating it, which
 * matters here more than anywhere else on the site: a truncate-and-reinsert
 * would be a window in which `/free-events` renders an event with no dates.
 */
class EventType extends BaseType
{
    public function key(): string
    {
        return 'event';
    }

    public function label(): string
    {
        return 'Event';
    }

    public function pluralLabel(): string
    {
        return 'Events';
    }

    public function hasSlug(): bool
    {
        return true;
    }

    public function schema(): Schema
    {
        return Schema::make([
            Text::make('title')->required()->max(255),
            Slug::make('slug')->from('title'),
            Textarea::make('excerpt')->required()->max(1000)->rows(4),
            RichText::make('body')->required()->sanitizeOnSave(false)->max(200000)
                ->governedBy(new SafeHtml),
            Text::make('cover_path')->max(255)
                ->help('Path under public/, e.g. media/events/example.jpg.'),
            Text::make('venue_name')->required()->max(255),
            Text::make('venue_address')->required()->max(255),
            Text::make('timezone')->required()->max(255),
            Text::make('meta_title')->required()->max(255),
            Textarea::make('meta_description')->required()->max(320)->rows(3),
            Textarea::make('geo_summary')->required()->max(1000)->rows(3),
            Repeater::make('occurrences')->max(60)->of(Schema::make([
                Text::make('id')->withLabel('Row')
                    ->help('Set by the site. Leave it alone; it is what keeps an existing date at its own address.'),
                DateTime::make('starts_at')->required(),
                DateTime::make('ends_at'),
                Slug::make('occurrence_slug')->required()
                    ->help('The address of this single date, e.g. bryant-park-movie-nights-2026-07-13.'),
            ]))->help('Every date this event runs. Each one gets its own page.'),
        ]);
    }

    public function source(): ContentSource
    {
        return new ModelSource(
            modelClass: Event::class,
            map: [
                'title' => 'title',
                'slug' => 'slug',
                'excerpt' => 'excerpt',
                'body' => 'body',
                'cover_path' => 'cover_path',
                'venue_name' => 'venue_name',
                'venue_address' => 'venue_address',
                'timezone' => 'timezone',
                'meta_title' => 'meta_title',
                'meta_description' => 'meta_description',
                'geo_summary' => 'geo_summary',
            ],
            relations: [
                'occurrences' => new RelationMap(
                    relation: 'occurrences',
                    map: [
                        'starts_at' => 'starts_at',
                        'ends_at' => 'ends_at',
                        'occurrence_slug' => 'occurrence_slug',
                    ],
                    keyField: 'id',
                ),
            ],
            slugColumn: 'slug',
            titleColumn: 'title',
        );
    }
}
