<?php

namespace App\Cms\Types;

use Eshlink\Cms\Schema\Fields\Image;
use Eshlink\Cms\Schema\Fields\Number;
use Eshlink\Cms\Schema\Fields\Repeater;
use Eshlink\Cms\Schema\Fields\Text;
use Eshlink\Cms\Schema\Fields\Url;
use Eshlink\Cms\Schema\Schema;

/**
 * The Instagram strip on the home page, which used to be a `File::json()` read
 * of `docs/wix-ref/instagram.json` on every request.
 *
 * That file is a one-off harvest from the old Wix site: a reference artifact
 * sitting in `docs/`, read by a controller, with the alt text derived on the
 * fly by truncating the first line of each caption. It is now a curated content
 * type — the alt text is a field Marina can fix, and the strip can be
 * reordered or trimmed without touching a JSON file nobody remembers exists.
 *
 * The defaults below are that harvest, imported once. `cms:import-instagram`
 * re-runs the same import from the same file if the strip is ever re-harvested.
 */
class InstagramFeedType extends PageSingleton
{
    public function key(): string
    {
        return 'instagram_feed';
    }

    public function label(): string
    {
        return 'Instagram';
    }

    public function blurb(): ?string
    {
        return 'The strip of posts on your home page.';
    }

    public function schema(): Schema
    {
        return Schema::make([
            Url::make('profile_url')->required()->max(255)
                ->help('Where every tile links. The strip is a link out, not a gallery.'),
            Repeater::make('items')->max(60)->of(Schema::make([
                Number::make('index')->integer()->min(1)
                    ->help('Only used in the tile\'s accessible label, e.g. "Instagram post 7".'),
                Image::make('path')->storesPath()->required()->max(255)
                    ->withLabel('The picture')
                    ->help('Choose one from your photos. Leave it alone and the picture on this page stays as it is.'),
                Text::make('alt')->required()->max(255)
                    ->help('What the picture shows, for anyone who cannot see it.'),
            ])),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function defaults(): array
    {
        return [
            'profile_url' => 'https://www.instagram.com/marina.newyorkcity/',
            'items' => [
                [
                    'index' => 1,
                    'path' => 'media/instagram/ig-01.jpg',
                    'alt' => 'One of those New York views that instantly feels iconic.',
                ],
                [
                    'index' => 2,
                    'path' => 'media/instagram/ig-02.jpg',
                    'alt' => 'Would you take the NYC Ferry to work if your daily commute looked like this?',
                ],
                [
                    'index' => 3,
                    'path' => 'media/instagram/ig-03.jpg',
                    'alt' => 'Would you believe this view is right above Times Square?',
                ],
                [
                    'index' => 4,
                    'path' => 'media/instagram/ig-04.jpg',
                    'alt' => 'Times Square in August feels electric after dark. Bright billboards, yellow taxis, warm summer nights, and that unmistak...',
                ],
                [
                    'index' => 5,
                    'path' => 'media/instagram/ig-05.jpg',
                    'alt' => 'Seeing the city from above with @flyhelitours was such a surreal experience, from the Chicago River to the skyline stret...',
                ],
                [
                    'index' => 6,
                    'path' => 'media/instagram/ig-06.jpg',
                    'alt' => 'Chicago after dark just feels different.',
                ],
                [
                    'index' => 7,
                    'path' => 'media/instagram/ig-07.jpg',
                    'alt' => 'Who else could fall in love with Chicago this fast?',
                ],
                [
                    'index' => 8,
                    'path' => 'media/instagram/ig-08.jpg',
                    'alt' => 'Who else is always in a New York State of mind?',
                ],
                [
                    'index' => 9,
                    'path' => 'media/instagram/ig-09.jpg',
                    'alt' => 'I partnered with @edgenyc to experience what’s new at one of the best views in New York City.',
                ],
                [
                    'index' => 10,
                    'path' => 'media/instagram/ig-10.jpg',
                    'alt' => 'Imagine yourself wandering through the West Village in late October.',
                ],
                [
                    'index' => 11,
                    'path' => 'media/instagram/ig-11.jpg',
                    'alt' => 'After a full day of rain, New York gave us this.',
                ],
                [
                    'index' => 12,
                    'path' => 'media/instagram/ig-12.jpg',
                    'alt' => 'Who said you need to spend a fortune to experience New York?',
                ],
                [
                    'index' => 13,
                    'path' => 'media/instagram/ig-13.jpg',
                    'alt' => 'Who would you spend a summer weekend in Washington, DC with?',
                ],
                [
                    'index' => 14,
                    'path' => 'media/instagram/ig-14.jpg',
                    'alt' => 'New Year’s Eve in Times Square… every day.',
                ],
                [
                    'index' => 15,
                    'path' => 'media/instagram/ig-15.jpg',
                    'alt' => 'Only 133 days until Christmas.',
                ],
                [
                    'index' => 16,
                    'path' => 'media/instagram/ig-16.jpg',
                    'alt' => 'Would you believe this is the same Rockefeller Center?',
                ],
                [
                    'index' => 17,
                    'path' => 'media/instagram/ig-17.jpg',
                    'alt' => 'Share this with someone whose heart is calling them to New York. ❤️🗽',
                ],
                [
                    'index' => 18,
                    'path' => 'media/instagram/ig-18.jpg',
                    'alt' => 'Share this with someone you’d escape to New York with. 🗽❤️',
                ],
                [
                    'index' => 19,
                    'path' => 'media/instagram/ig-19.jpg',
                    'alt' => 'There’s something so comforting about watching the rain take over New York from a warm, cozy home. 🌧️🗽',
                ],
                [
                    'index' => 20,
                    'path' => 'media/instagram/ig-20.jpg',
                    'alt' => 'Share this with someone whose heart belongs in New York. ❤️🗽',
                ],
                [
                    'index' => 21,
                    'path' => 'media/instagram/ig-21.jpg',
                    'alt' => 'The city is waking up, the streets are coming alive, and another New York story is about to begin.',
                ],
                [
                    'index' => 22,
                    'path' => 'media/instagram/ig-22.jpg',
                    'alt' => 'Stuck in traffic on 42nd Street, but with Grand Central looking this good, I’m not even complaining. 😍',
                ],
                [
                    'index' => 23,
                    'path' => 'media/instagram/ig-23.jpg',
                    'alt' => 'Last night’s storm over Manhattan, August 8, 2026',
                ],
                [
                    'index' => 24,
                    'path' => 'media/instagram/ig-24.jpg',
                    'alt' => 'Just minutes after the severe thunderstorm warning was issued, Manhattan was swallowed by dark skies, heavy rain, and po...',
                ],
                [
                    'index' => 25,
                    'path' => 'media/instagram/ig-25.jpg',
                    'alt' => 'Would you rather see Manhattan in the sunshine or the rain?',
                ],
                [
                    'index' => 26,
                    'path' => 'media/instagram/ig-26.jpg',
                    'alt' => 'Share this with someone who loves autumn.',
                ],
                [
                    'index' => 27,
                    'path' => 'media/instagram/ig-27.jpg',
                    'alt' => 'Long evenings, warm nights, and a city that somehow feels even more alive.',
                ],
                [
                    'index' => 28,
                    'path' => 'media/instagram/ig-28.jpg',
                    'alt' => 'You finally made it to New York… but which season are you choosing?',
                ],
                [
                    'index' => 29,
                    'path' => 'media/instagram/ig-29.jpg',
                    'alt' => 'Sometimes all it takes is opening the window.',
                ],
                [
                    'index' => 30,
                    'path' => 'media/instagram/ig-30.jpg',
                    'alt' => 'There’s just something about New York during the holidays that stays with you. The lights, the windows, the snow, the gi...',
                ],
            ],
        ];
    }

    /**
     * The strip has no page of its own: it is drawn on the home page, which is
     * where a preview of it has to be drawn too.
     *
     * @param  array<string, mixed>  $entry
     */
    public function publicPath(array $entry): ?string
    {
        return '/';
    }
}
