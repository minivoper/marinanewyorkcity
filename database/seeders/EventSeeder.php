<?php

namespace Database\Seeders;

use App\Models\Event;
use Carbon\CarbonImmutable;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->events() as $eventData) {
            $occurrences = $eventData['occurrences'];
            unset($eventData['occurrences']);

            $event = Event::query()->updateOrCreate(['slug' => $eventData['slug']], $eventData);
            $event->occurrences()->delete();
            $event->occurrences()->createMany($occurrences);
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function events(): array
    {
        return [
            [
                'slug' => 'nyc-pride-march',
                'cover_path' => 'media/events/e628c7_c2cde2b804ea4f6a8d0d6e7dec9d4f2f.jpg',
                'title' => 'NYC Pride March',
                'excerpt' => 'The NYC Pride March returns to Manhattan on June 28, 2026, beginning at noon near Fifth Avenue and West 26th Street.',
                'body' => '<p>The NYC Pride March is an annual celebration and civil-rights demonstration in Manhattan. The 2026 march steps off at noon on Sunday, June 28.</p><p>Expect significant crowds, street closures, and changes to nearby transit. Review the official NYC Pride guidance for the current route, accessible viewing information, and participation details before attending.</p>',
                'venue_name' => 'NYC Pride March starting point',
                'venue_address' => 'W 26th St & 5th Ave, New York, NY 10010',
                'timezone' => 'America/New_York',
                'meta_title' => 'NYC Pride March 2026 Date and Details',
                'meta_description' => 'The NYC Pride March takes place June 28, 2026, starting at noon near Fifth Avenue and West 26th Street in Manhattan.',
                'geo_summary' => 'A Pride march through Manhattan beginning near Fifth Avenue and West 26th Street.',
                'occurrences' => [[
                    'starts_at' => '2026-06-28 12:00:00',
                    'ends_at' => '2026-06-28 18:00:00',
                    'occurrence_slug' => 'nyc-pride-march-2026-06-28',
                ]],
            ],
            [
                'slug' => 'carnegie-hall-citywide-the-knights',
                'cover_path' => 'media/events/e628c7_2542eb3d17744666ad81f69f9a197a4c.webp',
                'title' => 'Carnegie Hall Citywide: The Knights',
                'excerpt' => 'The Knights bring a free Carnegie Hall Citywide performance to Bryant Park on July 3, 2026.',
                'body' => '<p>The Knights perform at Bryant Park as part of Carnegie Hall Citywide, presenting a free outdoor program in the center of Midtown.</p><p>Bring a blanket and confirm Bryant Park’s day-of weather and lawn guidance before arriving. Program timing may be adjusted for conditions.</p>',
                'venue_name' => 'Bryant Park',
                'venue_address' => 'Bryant Park, New York, NY 10018',
                'timezone' => 'America/New_York',
                'meta_title' => 'The Knights at Bryant Park — July 3, 2026',
                'meta_description' => 'See The Knights in a free Carnegie Hall Citywide concert at Bryant Park on July 3, 2026.',
                'geo_summary' => 'A free outdoor Carnegie Hall Citywide concert in Bryant Park, Midtown Manhattan.',
                'occurrences' => [[
                    'starts_at' => '2026-07-03 19:00:00',
                    'ends_at' => null,
                    'occurrence_slug' => 'carnegie-hall-citywide-the-knights-2026-07-03',
                ]],
            ],
            [
                'slug' => 'bryant-park-movie-nights',
                'cover_path' => 'media/events/e628c7_9e5acf1ec5404ee29a1d45aa7a99c85a.jpg',
                'title' => 'Bryant Park Movie Nights',
                'excerpt' => 'Bryant Park’s Monday movie series returns for summer 2026, with the lawn opening at 5 p.m. for outdoor screenings after sunset.',
                'body' => '<p>Bryant Park Movie Nights brings feature films to the lawn on Monday evenings. The seeded 2026 series runs from July 13 through September 28, with each occurrence beginning at the 5 p.m. lawn-opening time.</p><p>The first films are <em>Wayne’s World</em> on July 13, <em>Good Morning, Vietnam</em> on July 20, and <em>The Truman Show</em> on July 27. Later listings remain available as series dates even when the film is still marked to be determined.</p><p>Films begin later in the evening. Bring a blanket and check Bryant Park for weather updates, entry checkpoints, and confirmed film times.</p>',
                'venue_name' => 'Bryant Park',
                'venue_address' => 'Bryant Park, New York, NY 10018',
                'timezone' => 'America/New_York',
                'meta_title' => 'Bryant Park Movie Nights 2026 Schedule',
                'meta_description' => 'Browse the 2026 Bryant Park Movie Nights dates, beginning Mondays at 5 p.m. from July 13 through September 28.',
                'geo_summary' => 'Free outdoor Monday movie nights on the Bryant Park lawn in Midtown Manhattan.',
                'occurrences' => $this->weeklyOccurrences(
                    '2026-07-13 17:00:00',
                    '2026-09-28 17:00:00',
                    'bryant-park-movie-nights',
                ),
            ],
            [
                'slug' => 'midtown-music',
                'cover_path' => 'media/events/e628c7_cb6e461d19be44a7979d2a72cce542f3.jpg',
                'title' => 'Midtown Music',
                'excerpt' => 'Midtown Music brings live Wednesday-evening performances to Bryant Park from June 10 through July 15, 2026.',
                'body' => '<p>Midtown Music is a Wednesday-evening series at Bryant Park, with performances scheduled at 7:30 p.m. from June 10 through July 15, 2026.</p><p>The outdoor setting makes the series an easy after-work stop in Midtown. Check Bryant Park’s calendar for each week’s performer and for weather-related updates.</p>',
                'venue_name' => 'Bryant Park',
                'venue_address' => 'Bryant Park, New York, NY 10018',
                'timezone' => 'America/New_York',
                'meta_title' => 'Midtown Music at Bryant Park 2026',
                'meta_description' => 'See Midtown Music at Bryant Park on Wednesday evenings from June 10 through July 15, 2026.',
                'geo_summary' => 'A Wednesday evening live-music series in Bryant Park, Midtown Manhattan.',
                'occurrences' => $this->weeklyOccurrences(
                    '2026-06-10 19:30:00',
                    '2026-07-15 19:30:00',
                    'midtown-music',
                ),
            ],
        ];
    }

    /**
     * @return array<int, array{starts_at: string, ends_at: null, occurrence_slug: string}>
     */
    private function weeklyOccurrences(string $startsAt, string $lastStartsAt, string $slug): array
    {
        $occurrences = [];
        $date = CarbonImmutable::parse($startsAt);
        $lastDate = CarbonImmutable::parse($lastStartsAt);

        while ($date->lessThanOrEqualTo($lastDate)) {
            $occurrences[] = [
                'starts_at' => $date->toDateTimeString(),
                'ends_at' => null,
                'occurrence_slug' => $slug.'-'.$date->toDateString(),
            ];

            $date = $date->addWeek();
        }

        return $occurrences;
    }
}
