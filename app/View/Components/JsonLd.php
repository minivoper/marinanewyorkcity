<?php

namespace App\View\Components;

use App\Models\Event;
use App\Models\EventOccurrence;
use App\Models\Post;
use Closure;
use Eshlink\Cms\Support\HostMode;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class JsonLd extends Component
{
    public string $json;

    public function __construct(
        public ?Post $post = null,
        public ?Event $event = null,
        public ?EventOccurrence $occurrence = null,
    ) {
        $this->json = json_encode(
            ['@context' => 'https://schema.org', '@graph' => $this->graph()],
            JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR,
        );
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.json-ld');
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function graph(): array
    {
        $baseUrl = rtrim(config('site.production_url'), '/');
        $personId = $baseUrl.'/#person';
        $organizationId = $baseUrl.'/#organization';
        $graph = [
            [
                '@type' => 'Person',
                '@id' => $personId,
                'name' => config('site.author'),
                'email' => config('site.email'),
                'sameAs' => config('site.socials'),
                'homeLocation' => ['@type' => 'Place', 'name' => 'New York City'],
            ],
            array_filter([
                '@type' => 'NewsMediaOrganization',
                '@id' => $organizationId,
                'name' => config('app.name'),
                'url' => $this->url($baseUrl),
                'email' => config('site.email'),
                'founder' => ['@id' => $personId],
                'sameAs' => config('site.socials'),
            ], fn (mixed $value): bool => $value !== null),
        ];

        if ($this->post) {
            $graph[] = array_filter([
                '@type' => in_array($this->post->schema_type, ['NewsArticle', 'Article'], true)
                    ? $this->post->schema_type
                    : 'Article',
                '@id' => $baseUrl.route('posts.show', $this->post->slug, false).'#article',
                'url' => $this->url($baseUrl.route('posts.show', $this->post->slug, false)),
                'headline' => $this->post->title,
                'description' => $this->post->meta_description,
                'datePublished' => $this->post->published_at->toIso8601String(),
                'dateModified' => $this->post->updated_at->toIso8601String(),
                'author' => ['@id' => $personId],
                'publisher' => ['@id' => $organizationId],
                'contentLocation' => ['@type' => 'Place', 'name' => $this->post->location_name ?: 'New York City'],
            ], fn (mixed $value): bool => $value !== null);
        }

        if ($this->event) {
            $occurrence = $this->occurrence ?: $this->event->occurrences->first();
            $eventUrl = $baseUrl.'/'.request()->path();
            $eventSchema = [
                '@type' => 'Event',
                '@id' => $eventUrl.'#event',
                'url' => $this->url($eventUrl),
                'name' => $this->event->title,
                'description' => $this->event->meta_description,
                'eventStatus' => 'https://schema.org/EventScheduled',
                'eventAttendanceMode' => 'https://schema.org/OfflineEventAttendanceMode',
                'location' => [
                    '@type' => 'Place',
                    'name' => $this->event->venue_name,
                    'address' => $this->event->venue_address,
                ],
                'organizer' => ['@id' => $organizationId],
            ];

            if ($occurrence) {
                $eventSchema['startDate'] = $occurrence->starts_at->toIso8601String();
                $eventSchema['endDate'] = $occurrence->ends_at?->toIso8601String();
            }

            $graph[] = array_filter($eventSchema, fn (mixed $value): bool => $value !== null);
        }

        return $graph;
    }

    /**
     * A crawlable `url` on production hosts, and null everywhere else.
     *
     * `@id` values keep the production URL because they are identifiers that
     * hold the graph together, not addresses anything follows. `url` is an
     * address, and on a preview host it would publish the real domain in
     * machine-readable form on a page that is meant to be undiscoverable.
     */
    private function url(string $url): ?string
    {
        return HostMode::canonicalsSuppressed() ? null : $url;
    }
}
