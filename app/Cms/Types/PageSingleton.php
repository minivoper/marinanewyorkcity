<?php

namespace App\Cms\Types;

use Eshlink\Cms\Contracts\ContentSource;
use Eshlink\Cms\Sources\EntrySource;

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
abstract class PageSingleton extends BaseType
{
    public function isSingleton(): bool
    {
        return true;
    }

    public function source(): ContentSource
    {
        return new EntrySource;
    }
}
