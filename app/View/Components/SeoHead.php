<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SeoHead extends Component
{
    public string $canonicalUrl;

    public string $imageUrl;

    public string $pageTitle;

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
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.seo-head');
    }
}
