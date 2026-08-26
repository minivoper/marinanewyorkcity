<?php

namespace Database\Seeders;

use App\Models\Post;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class PostSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->posts() as $post) {
            $post['body'] = $this->bodyFor($post['slug'], $post['body']);

            Post::query()->updateOrCreate(['slug' => $post['slug']], $post);
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function posts(): array
    {
        return [
            [
                'type' => Post::TYPE_NEWS,
                'slug' => 'experience-summer-jazz-magic-at-abrons-arts-center-s-free-series-in-2026',
                'cover_path' => 'media/posts/e628c7_e49a12092e054bc8917e0b1e5cc96f48.png',
                'title' => "Experience Summer Jazz Magic at Abrons Arts Center's Free Series in 2026",
                'excerpt' => 'Spend your warm summer nights with free live jazz under the Lower East Side sky! Abrons Arts Center announces its "Summer in the Plaza" concert series on July 17 and August 15, featuring multi-instrumentalist Zacchae’us Paul and special guests. RSVP for free today.',
                'body' => <<<'HTML'
<p>Abrons Arts Center is bringing live jazz outdoors for Summer in the Plaza, a free series at the Miriam and Harold Steinberg Plaza on Manhattan’s Lower East Side.</p>
<p>The 2026 program centers multi-instrumentalist, singer, and songwriter Zacchae’us Paul alongside special guests. The evenings are designed for the whole family, with music in the open air on July 17 and August 15.</p>
<p>Admission is free with an RSVP. Check Abrons Arts Center for the latest performance details and arrival information before heading downtown.</p>
HTML,
                'published_at' => '2026-06-26 09:00:00',
                'read_minutes' => 3,
                'meta_title' => 'Free Summer Jazz at Abrons Arts Center in 2026',
                'meta_description' => 'Plan a free Lower East Side jazz night at Abrons Arts Center’s Summer in the Plaza series in July and August 2026.',
                'geo_summary' => 'Free outdoor jazz at Abrons Arts Center on the Lower East Side of Manhattan.',
                'location_name' => 'Lower East Side, Manhattan',
                'schema_type' => 'NewsArticle',
            ],
            [
                'type' => Post::TYPE_NEWS,
                'slug' => 'experience-the-vibrancy-of-west-side-fest-2026-with-free-cultural-events-and-art-activities',
                'cover_path' => 'media/posts/e628c7_c2f4e3fb8db84c3b889b0a786f7eb51e.png',
                'title' => 'Experience the Vibrancy of West Side Fest 2026 with Free Cultural Events and Art Activities',
                'excerpt' => 'West Side Fest returns July 10–12, 2026 with free artmaking, workshops, performances, dancing, crafts, and special programs across Manhattan’s West Side cultural institutions.',
                'body' => <<<'HTML'
<p>West Side Fest returns July 10 through July 12, 2026 for a free, multi-site celebration of arts and culture across Manhattan’s West Side.</p>
<p>Organizations in the West Side Cultural Network are presenting artmaking, workshops, performances, dancing, crafts, and other programs. Participating destinations span museums, galleries, parks, theaters, and community spaces, making the festival easy to explore as a neighborhood itinerary.</p>
<p>Individual activities may have their own hours, capacity limits, or reservation requirements. Review the festival calendar and each participating organization’s details before your visit.</p>
HTML,
                'published_at' => '2026-06-24 09:00:00',
                'read_minutes' => 4,
                'meta_title' => 'West Side Fest 2026 Free NYC Arts Events',
                'meta_description' => 'Explore West Side Fest in Manhattan from July 10–12, 2026, with free cultural events, workshops, artmaking, and performances.',
                'geo_summary' => 'A free arts and culture festival across Manhattan’s West Side.',
                'location_name' => 'West Side, Manhattan',
                'schema_type' => 'NewsArticle',
            ],
            [
                'type' => Post::TYPE_NEWS,
                'slug' => 'experience-the-excitement-of-fifa-world-cup-2026-at-hudson-yards-with-unique-fan-events',
                'cover_path' => 'media/posts/e628c7_6519a0c0990848b687966beeef1f3d73.jpg',
                'title' => 'Experience the Excitement of FIFA World Cup 2026 at Hudson Yards with Unique Fan Events',
                'excerpt' => 'Hudson Yards brings FIFA World Cup 2026 energy to Manhattan with outdoor match screenings, fan experiences, flags at Vessel, and official merchandise during the tournament.',
                'body' => <<<'HTML'
<p>Hudson Yards is turning its public spaces into a Manhattan gathering place for the FIFA World Cup 2026, which runs from June 11 through July 19.</p>
<p>The program includes outdoor watch parties on the large screen, fan activations, and official merchandise. Visitors can also take part in the Fly Your Flag experience at Vessel and look for additional tournament-themed activities around The Shops at Hudson Yards.</p>
<p>Match schedules and individual activations can change, so confirm the day’s program with Hudson Yards before traveling. Arrive early for popular matches and prepare for outdoor conditions.</p>
HTML,
                'published_at' => '2026-06-19 09:00:00',
                'read_minutes' => 3,
                'meta_title' => 'FIFA World Cup 2026 Fan Events at Hudson Yards',
                'meta_description' => 'Find World Cup 2026 watch parties, fan experiences, and official merchandise at Hudson Yards in New York City.',
                'geo_summary' => 'World Cup watch parties and fan activities at Hudson Yards in Manhattan.',
                'location_name' => 'Hudson Yards',
                'schema_type' => 'NewsArticle',
            ],
            [
                'type' => Post::TYPE_GUIDE,
                'slug' => '1-day-in-new-york-city-example',
                'cover_path' => 'media/posts/e628c7_3a6853e78b2d4eb9a542319ab7c27352.jpg',
                'title' => '1 Day in New York City: Example Itinerary',
                'excerpt' => 'A simple one-day New York City route connecting classic Midtown sights, Central Park, and an evening skyline walk.',
                'body' => <<<'HTML'
<p>Start the morning in Central Park, entering near Columbus Circle and walking through the southern paths before Midtown becomes busy.</p>
<p>Continue down Fifth Avenue toward Rockefeller Center, Bryant Park, and the New York Public Library. This keeps the daytime route walkable while connecting several familiar New York landmarks.</p>
<p>Finish downtown with a sunset walk near the waterfront or choose an observation deck for a wide city view. Leave space between stops: one rewarding New York day is better than racing through a checklist.</p>
HTML,
                'published_at' => '2026-05-12 09:00:00',
                'read_minutes' => 1,
                'meta_title' => '1 Day in New York City Example Itinerary',
                'meta_description' => 'Follow a simple one-day New York City itinerary with Central Park, Midtown landmarks, and an evening skyline view.',
                'geo_summary' => 'A one-day visitor itinerary through Central Park, Midtown, and the New York City waterfront.',
                'location_name' => 'New York City',
                'schema_type' => 'Article',
            ],
            [
                'type' => Post::TYPE_NEWS,
                'slug' => 'lincoln-center-begins-major-west-side-transformation-in-new-york-city',
                'cover_path' => 'media/posts/e628c7_e6107b3de5d94d50a22216d5c1f50bc2.png',
                'title' => 'Lincoln Center Begins Major West Side Transformation in New York City',
                'excerpt' => 'Lincoln Center has broken ground on a major redesign of its western campus, with new gardens, more welcoming Amsterdam Avenue access, and an outdoor performance venue planned for summer 2028.',
                'body' => <<<'HTML'
<p>Lincoln Center broke ground on May 11, 2026 for a major transformation of the western side of its campus facing Amsterdam Avenue.</p>
<p>The project will reimagine Damrosch Park as the Stavros Niarchos Foundation Gardens, add greener community gathering spaces, and create more welcoming connections from the surrounding neighborhood. A new outdoor venue, The Baron Theater, is planned as a central part of the design.</p>
<p>The initiative follows extensive community feedback and is expected to open in summer 2028. During construction, visitors should check Lincoln Center’s current access information before attending a performance or public program.</p>
HTML,
                'published_at' => '2026-05-11 12:00:00',
                'read_minutes' => 2,
                'meta_title' => 'Lincoln Center Begins West Side Transformation',
                'meta_description' => 'Lincoln Center has begun a West Side campus redesign with gardens, better neighborhood access, and a new outdoor venue.',
                'geo_summary' => 'A major redevelopment of Lincoln Center’s Amsterdam Avenue side and Damrosch Park.',
                'location_name' => 'Lincoln Center, Manhattan',
                'schema_type' => 'NewsArticle',
            ],
            [
                'type' => Post::TYPE_NEWS,
                'slug' => 'times-square-s-midnight-moment-becomes-a-cosmic-farewell-this-may',
                'cover_path' => 'media/posts/e628c7_9b083a67e8094392a5cf9af710d73c8f.png',
                'title' => "Times Square's Midnight Moment Becomes a Cosmic Farewell This May",
                'excerpt' => 'Yael Bartana’s Farewell takes over Times Square’s digital screens nightly from 11:57 p.m. to midnight throughout May 2026 with a vision of humanity leaving Earth.',
                'body' => <<<'HTML'
<p>Times Square’s Midnight Moment presents <em>Farewell</em> by artist Yael Bartana every night from May 1 through May 31, 2026.</p>
<p>The synchronized video appears from 11:57 p.m. to midnight on digital displays along Broadway between 41st and 49th Streets. The work imagines the ritual before a fictional generation ship departs Earth for remote galaxies, combining movement, symbolism, mourning, and hope.</p>
<p>The outdoor presentation is free and can be viewed from public spaces in Times Square. Arrive a few minutes before 11:57 p.m. to find a clear view of multiple screens.</p>
HTML,
                'published_at' => '2026-05-11 09:00:00',
                'read_minutes' => 2,
                'meta_title' => 'Times Square Midnight Moment: Farewell, May 2026',
                'meta_description' => 'See Yael Bartana’s Farewell across Times Square screens nightly from 11:57 p.m. to midnight in May 2026.',
                'geo_summary' => 'A free nightly digital art presentation across Times Square in Manhattan.',
                'location_name' => 'Times Square',
                'schema_type' => 'NewsArticle',
            ],
            [
                'type' => Post::TYPE_NEWS,
                'slug' => 'how-to-create-a-cinematic-video-with-an-iphone',
                'title' => 'How to Create a Cinematic Video with an iPhone',
                'excerpt' => 'Use intentional movement, light, composition, sound, and a simple edit to make more cinematic iPhone video in New York City or anywhere else.',
                'body' => <<<'HTML'
<p>A cinematic iPhone video starts with a clear idea. Decide what the scene should feel like, then choose a short sequence of wide, medium, and close details that supports that mood.</p>
<p>Clean the lens, lock focus and exposure when the shot needs consistency, and move slowly. Use available light deliberately: early morning, late afternoon, window light, reflections, and New York’s illuminated streets can all shape a scene without extra equipment.</p>
<p>In the edit, keep only the shots that advance the visual story. Cut with the rhythm of the movement or music, preserve useful natural sound, and use color adjustments with restraint so skin tones and city lights still feel believable.</p>
HTML,
                'published_at' => '2026-04-30 09:00:00',
                'read_minutes' => 1,
                'meta_title' => 'How to Create Cinematic Video with an iPhone',
                'meta_description' => 'A concise guide to planning, shooting, and editing cinematic iPhone video with intentional movement, light, and sound.',
                'geo_summary' => 'Practical cinematic iPhone videography guidance from a New York City visual storyteller.',
                'location_name' => null,
                'schema_type' => 'Article',
            ],
        ];
    }

    private function bodyFor(string $slug, string $fallback): string
    {
        $path = database_path("data/posts/{$slug}.html");

        return File::exists($path) ? File::get($path) : $fallback;
    }
}
