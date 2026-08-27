<?php

namespace App\Cms\Types;

use App\Models\Page;
use Eshlink\Cms\Contracts\ContentSource;
use Eshlink\Cms\Contracts\PubliclyRoutable;
use Eshlink\Cms\Rules\NoDashes;
use Eshlink\Cms\Schema\Fields\RichText;
use Eshlink\Cms\Schema\Fields\Slug;
use Eshlink\Cms\Schema\Fields\Text;
use Eshlink\Cms\Schema\Fields\Textarea;
use Eshlink\Cms\Schema\Schema;
use Eshlink\Cms\Sources\ModelSource;
use Eshlink\Cms\Support\Html;
use Eshlink\Cms\Support\SiteMap;

/**
 * Marina's long-form pages — privacy policy, terms, about, work with me,
 * press — wrapped rather than migrated.
 *
 * `PageController::renderPage()` still does `Page::where('slug', ...)` and
 * `pages/show.blade.php` still echoes `{!! $page->body !!}`. Nothing in the
 * public read path learns that a CMS exists; what it gains is a draft layer,
 * version history and an audit trail in the `cms_*` side tables.
 *
 * The `pages` table has no visibility column, so every row is live the moment
 * it exists. That is why creating a page through the CMS is refused by
 * `ModelSource` — there would be no way to keep a new page out of the public
 * site while it was being written.
 */
class PageType extends BaseType implements PubliclyRoutable
{
    /**
     * The five long-form pages that have a route, and the path each renders
     * at.
     *
     * `PageController` names them one method at a time — there is no
     * `/{slug}` catch-all — so a row in `pages` is only reachable if it is one
     * of these. An allowlist rather than `'/'.$slug` because a page that is
     * not routed must say so instead of pointing a preview at a 404.
     *
     * @var array<string, string>
     */
    private const ROUTED = [
        'privacy-policy' => '/privacy-policy',
        'terms-and-conditions' => '/terms-and-conditions',
        'about' => '/about',
        'work-with-me' => '/work-with-me',
        'press' => '/press',
    ];

    public function key(): string
    {
        return 'page';
    }

    public function label(): string
    {
        return 'Page';
    }

    public function pluralLabel(): string
    {
        return 'Pages';
    }

    public function blurb(): ?string
    {
        return 'About, Press, Privacy and Terms.';
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
                ->help('The address of the page. Changing it breaks links people have already saved.'),
            RichText::make('body')->required()->sanitizeOnSave(false)->max(200000)
                ->allow(array_keys(Html::ALLOWED))
                ->help('The page copy. Markup outside the allowlist is refused by name rather than quietly stripped.'),
            Text::make('meta_title')->required()->max(255),
            Textarea::make('meta_description')->required()->max(320)->rows(3),
        ]);
    }

    public function rules(): array
    {
        return [new NoDashes(['title', 'meta_title', 'meta_description'])];
    }

    public function source(): ContentSource
    {
        return new ModelSource(
            modelClass: Page::class,
            map: [
                'title' => 'title',
                'slug' => 'slug',
                'body' => 'body',
                'meta_title' => 'meta_title',
                'meta_description' => 'meta_description',
            ],
            slugColumn: 'slug',
            titleColumn: 'title',
        );
    }

    /**
     * @param  array<string, mixed>  $entry
     */
    public function publicPath(array $entry): ?string
    {
        $slug = $entry['data']['slug'] ?? $entry['slug'] ?? null;

        return is_string($slug) ? (self::ROUTED[$slug] ?? null) : null;
    }
}
