<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Post;
use Eshlink\Cms\Support\HostMode;
use Illuminate\Http\Response;

/**
 * The discovery surface, which exists only on the production host.
 *
 * A sitemap or an llms.txt served from a preview host is a machine-readable
 * index of a site that is not meant to be found yet, and it lists the real
 * domain while it is at it. These endpoints answer on marinanewyorkcity.com
 * and 404 anywhere else; robots.txt is the exception, because a crawler that
 * reaches a preview host needs to be told "no" rather than "no such file".
 */
class SeoController extends Controller
{
    public function sitemap(): Response
    {
        abort_unless(HostMode::isProduction(), 404);

        $baseUrl = rtrim(config('site.production_url'), '/');
        $urls = collect([
            $baseUrl,
            $baseUrl.route('posts.index', absolute: false),
            $baseUrl.route('events.index', absolute: false),
            $baseUrl.route('pages.privacy', absolute: false),
            $baseUrl.route('pages.terms', absolute: false),
            $baseUrl.route('pages.about', absolute: false),
            $baseUrl.route('pages.press', absolute: false),
            $baseUrl.route('pages.work', absolute: false),
        ]);

        $postUrls = Post::published()
            ->latest('published_at')
            ->get(['slug', 'updated_at'])
            ->map(fn (Post $post): array => [
                'location' => $baseUrl.route('posts.show', $post->slug, false),
                'last_modified' => $post->updated_at,
            ]);

        $events = Event::query()->with('occurrences:id,event_id,occurrence_slug,updated_at')->get(['id', 'slug', 'updated_at']);
        $eventUrls = $events->flatMap(function (Event $event) use ($baseUrl): array {
            $urls = [[
                'location' => $baseUrl.route('events.show', $event->slug, false),
                'last_modified' => $event->updated_at,
            ]];

            foreach ($event->occurrences as $occurrence) {
                $urls[] = [
                    'location' => $baseUrl.route('events.show', $occurrence->occurrence_slug, false),
                    'last_modified' => $occurrence->updated_at,
                ];
            }

            return $urls;
        });

        $entries = $urls->map(fn (string $url): array => ['location' => $url, 'last_modified' => null])
            ->concat($postUrls)
            ->concat($eventUrls);

        return response()
            ->view('seo.sitemap', compact('entries'))
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    public function robots(): Response
    {
        if (! HostMode::isProduction()) {
            return response("User-agent: *\nDisallow: /\n", 200)
                ->header('Content-Type', 'text/plain; charset=UTF-8');
        }

        $agents = [
            'Googlebot',
            'GPTBot',
            'ChatGPT-User',
            'PerplexityBot',
            'ClaudeBot',
            'Google-Extended',
            'Applebot-Extended',
            'Bytespider',
        ];

        $directives = collect($agents)
            ->map(fn (string $agent): string => "User-agent: {$agent}\nAllow: /")
            ->push('Sitemap: '.config('site.production_url').'/sitemap.xml')
            ->implode("\n\n");

        return response($directives."\n", 200)->header('Content-Type', 'text/plain; charset=UTF-8');
    }

    public function llms(): Response
    {
        abort_unless(HostMode::isProduction(), 404);

        $content = <<<'TEXT'
Marina Kapler is the New York City content creator behind @marina.newyorkcity, sharing cinematic city guides, culture, events, and visual stories.

marina.newyorkcity is an independent New York City media brand by Marina Kapler. Coverage includes NYC news, practical guides, public events, arts and culture, luxury lifestyle, technology, and cinematic iPhone videography.

Official website: https://marinanewyorkcity.com
Contact: info@marinanewyorkcity.com
Blog: https://marinanewyorkcity.com/blog
Events: https://marinanewyorkcity.com/event-list
RSS: https://marinanewyorkcity.com/feed.xml
JSON Feed: https://marinanewyorkcity.com/feed.json
TEXT;

        return response($content."\n", 200)->header('Content-Type', 'text/plain; charset=UTF-8');
    }
}
