<?php

namespace App\Cms\Types;

use Eshlink\Cms\Contracts\ContentSource;
use Eshlink\Cms\Contracts\PubliclyRoutable;
use Eshlink\Cms\Sources\EntrySource;
use Eshlink\Cms\Support\SiteMap;

/**
 * Base for the pages whose copy used to live as literals inside a Blade
 * template: home, travel-usa, shop, merch, how-i-create, free-events,
 * accessibility-statement and the news-and-press intro.
 *
 * Each is exactly one page, so it is a singleton with no address of its own:
 * the route still belongs to `routes/web.php` and the controller still renders
 * the same view. The only thing that moved is where the words come from.
 *
 * They are backed by `EntrySource` rather than `ModelSource` because there is
 * no model to wrap. Marina's `pages` table holds her long-form legal and about
 * pages, which are already editable through {@see PageType}; these eight were
 * never rows in it.
 */
abstract class PageSingleton extends BaseType implements PubliclyRoutable
{
    public function isSingleton(): bool
    {
        return true;
    }

    public function source(): ContentSource
    {
        return new EntrySource;
    }

    /**
     * Every subclass of this IS one page of Marina's site — she can open it in
     * a browser and point at it — so the group is settled here rather than
     * restated nine times. Declared explicitly all the same: `SiteMap` would
     * infer it from `isSingleton()`, but a page that stopped being a singleton
     * would then quietly change groups, and that is not what "this is a page"
     * means.
     */
    public function group(): ?string
    {
        return SiteMap::GROUP_PAGE;
    }

    /**
     * Each of these IS one page, so each names the address its words are drawn
     * at — which is what gives it a live preview pane in the editor. See
     * {@see PubliclyRoutable}.
     *
     * The entry is ignored by every one of them: a singleton has no slug, and
     * the route it renders at is a literal in `routes/web.php`. Declared
     * abstract rather than guessed from the key, because "the address of the
     * shop page" is a fact about this site's routes and nothing else.
     *
     * @param  array<string, mixed>  $entry
     */
    abstract public function publicPath(array $entry): ?string;
}
