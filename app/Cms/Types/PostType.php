<?php

namespace App\Cms\Types;

use App\Models\Post;
use Eshlink\Cms\Contracts\ContentSource;
use Eshlink\Cms\Contracts\PubliclyRoutable;
use Eshlink\Cms\Rules\NoDashes;
use Eshlink\Cms\Rules\WordBand;
use Eshlink\Cms\Schema\Fields\DateTime;
use Eshlink\Cms\Schema\Fields\Image;
use Eshlink\Cms\Schema\Fields\Number;
use Eshlink\Cms\Schema\Fields\RichText;
use Eshlink\Cms\Schema\Fields\Select;
use Eshlink\Cms\Schema\Fields\Slug;
use Eshlink\Cms\Schema\Fields\Text;
use Eshlink\Cms\Schema\Fields\Textarea;
use Eshlink\Cms\Schema\Schema;
use Eshlink\Cms\Sources\ModelSource;
use Eshlink\Cms\Support\Html;
use Eshlink\Cms\Support\SiteMap;
use Illuminate\Database\Eloquent\Builder;

/**
 * News stories and NYC guides, wrapped over the existing `Post` model.
 *
 * One `type` select drives both, exactly as the column does today: `/news`,
 * `/guides` and the home page's two card grids all filter on it, so making it
 * a field rather than two content types keeps a single row addressable by a
 * single slug no matter which listing it appears in.
 *
 * The published scope is Marina's own `Post::published()` — deliberately hers
 * and not a `whereNotNull('published_at')` of ours. Her scope compares against
 * `now()`, so a post dated in the future is scheduled rather than live, and
 * the CMS must read the site exactly as the site reads itself.
 */
class PostType extends BaseType implements PubliclyRoutable
{
    /**
     * `/post/{slug}` — the one route both news and guides render at, whichever
     * listing they appear in.
     *
     * The posted slug wins over the stored one, so renaming a story and
     * looking at it shows the story at its new address. A story with no slug
     * at all has no address yet, and null is the honest answer.
     *
     * @param  array<string, mixed>  $entry
     */
    public function publicPath(array $entry): ?string
    {
        $slug = $entry['data']['slug'] ?? $entry['slug'] ?? null;

        return is_string($slug) && $slug !== '' ? '/post/'.$slug : null;
    }

    public function key(): string
    {
        return 'post';
    }

    public function label(): string
    {
        return 'Story';
    }

    public function pluralLabel(): string
    {
        return 'Stories';
    }

    public function blurb(): ?string
    {
        return 'Your news posts and your city guides.';
    }

    public function group(): ?string
    {
        return SiteMap::GROUP_COLLECTION;
    }

    public function hasSlug(): bool
    {
        return true;
    }

    public function schema(): Schema
    {
        return Schema::make([
            Text::make('title')->required()->max(255),
            Slug::make('slug')->from('title')
                ->help('The address of the story. Changing it breaks links people have already shared.'),
            Select::make('type')->required()->options([
                Post::TYPE_NEWS => 'News',
                Post::TYPE_GUIDE => 'Guide',
            ])->help('News shows under "What\'s going on" and on News and Press; guides show under "NYC guides".'),
            Textarea::make('excerpt')->required()->max(1000)->rows(4),
            RichText::make('body')->required()->sanitizeOnSave(false)->max(200000)
                ->allow(array_keys(Html::ALLOWED)),
            Image::make('cover_path')->storesPath()->max(255)
                ->withLabel('Cover picture')
                ->help('Choose one from your photos. Leave it alone and the picture on this page stays as it is.'),
            DateTime::make('published_at')
                ->help('A date in the future keeps the story off the site until it arrives.'),
            Number::make('read_minutes')->integer()->min(1)->max(240),
            Text::make('meta_title')->required()->max(255),
            Textarea::make('meta_description')->required()->max(320)->rows(3),
            Textarea::make('geo_summary')->required()->max(1000)->rows(3)
                ->help('One or two sentences an AI search engine can quote as the answer.'),
            Text::make('location_name')->max(255),
            Select::make('schema_type')->options([
                'NewsArticle' => 'NewsArticle',
                'Article' => 'Article',
            ]),
        ]);
    }

    public function rules(): array
    {
        return [
            new NoDashes(['title', 'excerpt', 'meta_title', 'meta_description', 'geo_summary', 'location_name']),
            new WordBand(10, 30, ['geo_summary']),
        ];
    }

    public function source(): ContentSource
    {
        return new ModelSource(
            modelClass: Post::class,
            map: [
                'title' => 'title',
                'slug' => 'slug',
                'type' => 'type',
                'excerpt' => 'excerpt',
                'body' => 'body',
                'cover_path' => 'cover_path',
                'published_at' => 'published_at',
                'read_minutes' => 'read_minutes',
                'meta_title' => 'meta_title',
                'meta_description' => 'meta_description',
                'geo_summary' => 'geo_summary',
                'location_name' => 'location_name',
                'schema_type' => 'schema_type',
            ],
            slugColumn: 'slug',
            titleColumn: 'title',
            publishedColumn: 'published_at',
            publishedScope: fn (Builder $query) => $query->published(),
        );
    }
}
