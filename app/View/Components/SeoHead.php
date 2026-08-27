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
        $this->imageUrl = $image
            ? (str_starts_with($image, 'http') ? $image : $baseUrl.'/'.ltrim($image, '/'))
            : $baseUrl.'/favicon.ico';
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
