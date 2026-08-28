<?php

namespace App\View\Components;

use Closure;
use Eshlink\Cms\Support\HostMode;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SeoHead extends Component
{
    public string $canonicalUrl;

    public string $imageUrl;

    /** Whether $imageUrl is the site's own 1200x630 card rather than a page's cover. */
    public bool $imageIsDefault;

    public string $pageTitle;

    /**
     * Whether the canonical link, its hreflang alternate and og:url are left
     * out entirely.
     *
     * True on every host that is not the production domain. A canonical tag
     * pointing at marinanewyorkcity.com is exactly how a preview host on
     * eshlink.com teaches a crawler that it exists, and no amount of
     * X-Robots-Tag undoes a URL that has already been discovered.
     */
    public bool $canonicalSuppressed;

    public function __construct(
        public ?string $title = null,
        public ?string $description = null,
        public ?string $canonical = null,
        public ?string $image = null,
        public string $type = 'website',
    ) {
        $baseUrl = rtrim(config('site.production_url'), '/');
        $path = request()->path() === '/' ? '' : '/'.request()->path();

        $this->pageTitle = $title ?: config('app.name');
        $this->description ??= 'New York City news, events, guides, and cinematic stories by Marina Kapler.';
        $this->canonicalUrl = $canonical ?: $baseUrl.$path;
        /*
         | A share card has to load from the host that actually served the page.
         | Production keeps the production URL — that is what the social meta is
         | for, and PhotoOnAPublishedPageTest pins it. Anywhere else the image
         | resolves against the host answering, because a preview host pointing
         | its og:image at marinanewyorkcity.com asks for a file that domain does
         | not serve yet, and the card comes back blank.
         |
         | This is not the canonical rule in reverse: a canonical teaches a
         | crawler the production URL exists, an image reference does not. If
         | anything this keeps one more production URL off the preview host.
         */
        $imageBase = HostMode::isProduction() ? $baseUrl : rtrim(HostMode::rootUrl(), '/');

        // The default was /favicon.ico — a 16px icon offered as a share card, and
        // for most of this site's life a zero-byte file.
        $this->imageIsDefault = $image === null || $image === '';

        $this->imageUrl = $this->imageIsDefault
            ? $imageBase.'/media/brand/og-image.jpg'
            : (str_starts_with($image, 'http') ? $image : $imageBase.'/'.ltrim($image, '/'));
        $this->canonicalSuppressed = HostMode::canonicalsSuppressed();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.seo-head');
    }
}
