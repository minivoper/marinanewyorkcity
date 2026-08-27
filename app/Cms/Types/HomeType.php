<?php

namespace App\Cms\Types;

use Eshlink\Cms\Rules\SafeHtml;
use Eshlink\Cms\Schema\Fields\Repeater;
use Eshlink\Cms\Schema\Fields\RichText;
use Eshlink\Cms\Schema\Fields\Text;
use Eshlink\Cms\Schema\Fields\Textarea;
use Eshlink\Cms\Schema\Schema;

/**
 * `/` — the hero, the two card grids' headings, three copy blocks and the
 * partner logo wall.
 *
 * The card grids themselves are not here: they are `Post::published()` queries
 * in `HomeController`, and the Instagram strip is its own type. What this owns
 * is every word and every image path that used to be a literal in
 * `home.blade.php`, including the 22 partner logos that were a hardcoded PHP
 * array at the top of the template.
 */
class HomeType extends PageSingleton
{
    public function key(): string
    {
        return 'home';
    }

    public function label(): string
    {
        return 'Home page';
    }

    public function schema(): Schema
    {
        return Schema::make([
            Text::make('hero_image')->required()->max(255)
                ->help('Path under public/, e.g. media/posts/example.jpg.'),
            Text::make('hero_image_alt')->required()->max(255),
            Text::make('hero_heading')->required()->max(120),
            Text::make('hero_subheading')->required()->max(255),

            Text::make('news_heading')->required()->max(120),
            Text::make('news_kicker')->max(255),

            Text::make('guides_heading')->required()->max(120),
            Text::make('guides_kicker')->max(255),

            Text::make('social_image')->required()->max(255),
            Text::make('social_image_alt')->required()->max(255),
            Text::make('social_heading')->required()->max(120),
            RichText::make('social_body')->required()->sanitizeOnSave(false)->max(20000)
                ->governedBy(new SafeHtml),

            Text::make('licensing_image')->required()->max(255),
            Text::make('licensing_image_alt')->required()->max(255),
            Text::make('licensing_heading')->required()->max(120),
            RichText::make('licensing_body')->required()->sanitizeOnSave(false)->max(20000)
                ->governedBy(new SafeHtml),
            Text::make('licensing_cta_label')->required()->max(60),

            Text::make('about_image')->required()->max(255),
            Text::make('about_image_alt')->required()->max(255),
            Text::make('about_heading')->required()->max(120),
            RichText::make('about_body')->required()->sanitizeOnSave(false)->max(20000)
                ->governedBy(new SafeHtml),

            Text::make('instagram_heading')->required()->max(120),

            Text::make('partners_heading')->required()->max(120),
            Text::make('partners_kicker')->max(255),
            Repeater::make('partner_logos')->max(120)->of(Schema::make([
                Text::make('file')->required()->max(255)
                    ->help('File name inside public/media/about.'),
                Text::make('alt')->required()->max(255),
            ])),

            Text::make('seo_title')->required()->max(255),
            Textarea::make('seo_description')->required()->max(320)->rows(3),
        ]);
    }

    /**
     * The copy blocks carry the indentation their Blade template gave them:
     * the template supplies the first line's indent and the closing newline,
     * every line after the first carries its own.
     *
     * @return array<string, mixed>
     */
    public function defaults(): array
    {
        return [
            'hero_image' => 'media/posts/e628c7_3a6853e78b2d4eb9a542319ab7c27352.jpg',
            'hero_image_alt' => 'New York City skyline',
            'hero_heading' => 'new york',
            'hero_subheading' => 'where magic begins',

            'news_heading' => "WHAT'S GOING ON",
            'news_kicker' => 'New York News and events',

            'guides_heading' => 'NYC GUIDES',
            'guides_kicker' => 'what to do | where to stay | what to eat',

            'social_image' => 'media/about/e628c7_a0ff2346577549efb596b2953cf5db24.jpg',
            'social_image_alt' => 'Cinematic New York City content',
            'social_heading' => 'SOCIAL MEDIA AND MORE',
            'social_body' => <<<'HTML'
<p>• Cinematic Storytelling: Crafting short-form video content (reels, shorts) where products and experiences are seamlessly integrated into authentic NYC narratives, avoiding overt advertising.</p>
                <p>• Luxury &amp; Lifestyle: Showcasing high-end experiences, products, and destinations with an elevated aesthetic.</p>
                <p>• Tech Integration: Demonstrating cutting-edge technology within real-world, aspirational contexts.</p>
                <p>• NYC Focus: Deep expertise in New York City visuals, events, and cultural experiences, offering unparalleled local insight.</p>
                <p>Extended Production Capabilities (Available Upon Request):</p>
                <p>In addition to core deliverables, Marina New York City offers access to a curated network of industry professionals to support higher-scale productions and specialized campaign needs.</p>
                <p>• Professional Photography &amp; Videography Team: Access to experienced photographers and videographers using high-end equipment (Sony, Canon systems) for commercial-grade production.</p>
                <p>• Licensed Drone Operations: FAA-compliant drone operators for cinematic aerial footage and dynamic city perspectives.</p>
                <p>• Original Music &amp; Sound Design: Custom compositions by professional musicians to create signature audio identities for campaigns and branded content.</p>
                <p>These services are available as add-ons and can be integrated into custom production packages based on campaign scope.</p>
HTML,

            'licensing_image' => 'media/about/e628c7_9a81cf94d27847e48a75b7ef7bdef6ff.jpg',
            'licensing_image_alt' => 'New York City sunset',
            'licensing_heading' => 'content licensing',
            'licensing_body' => '<p>Licensing cinematic New York City photo and video content for editorial, commercial, tourism, hospitality, media, and digital campaigns. Custom footage, event coverage, and archive content available for licensed use across social, web, advertising, and broadcast platforms.</p>',
            'licensing_cta_label' => 'Contact us',

            'about_image' => 'media/about/e628c7_ac7a17d721e543e280e1c0932ce97771.png',
            'about_image_alt' => 'Marina Kapler',
            'about_heading' => 'about me',
            'about_body' => <<<'HTML'
<p>Marina Kapler is the creator behind @marina.newyorkcity. A NYC-based content creator, marketing professional, and visual storyteller with a background in media, psychology, and design.</p>
                <p>Specializing in cinematic iPhone videography, I consistently produce short-form content that goes viral, with individual Reels reaching over 800K views and more than 5M monthly views across my platforms.</p>
                <p>My content doesn't look like advertising. It looks like New York.</p>
HTML,

            'instagram_heading' => 'Follow us on Instagram',

            'partners_heading' => 'OUR CLIENTS AND PARTNERS',
            'partners_kicker' => 'We believe every client is a valuable long-term partner.',
            'partner_logos' => $this->partnerLogos(),

            'seo_title' => 'HOME | marina.newyorkcity',
            'seo_description' => 'New York City news, events, guides, and cinematic stories by Marina Kapler.',
        ];
    }

    /**
     * @return array<int, array{file: string, alt: string}>
     */
    private function partnerLogos(): array
    {
        $files = [
            'e628c7_fcebfbb5c8bb4397b080bdca7bdffb4b.png',
            'e628c7_7ea067f9a024426082d23fcd2751bd28.png',
            'e628c7_bc2d366f915f4c699d5fa6c8890fa948.png',
            'e628c7_69394db87db0469fa261462937994f6e.png',
            'e628c7_7f495f107f134fe386b3c93c687418dd.png',
            'e628c7_3c12e21a92474ac99615f8147d8c6110.png',
            'e628c7_e040479eacfc44eea5268f403ed55e21.png',
            'e628c7_42855d22127a4f05843c845d4670a8e3.png',
            'e628c7_cdbe2df1db6a48a19d220b858619b0f4.png',
            'e628c7_a3e5d7952b254201bec5d85d042fe33e.png',
            'e628c7_23a09831f672420ea3e40591a79c296b.png',
            'e628c7_d12527494e37415ea3b9b9e6def457be.png',
            'e628c7_8582203f5809464585d86248be7d124b.png',
            'e628c7_0850b80059f943108b866232ed233ebb.png',
            'e628c7_d2cbda09a54a40f888f8e3231b6786a8.png',
            'e628c7_14039dffd1554404821871ff7df7d666.png',
            'e628c7_619c8ccad2b44ca097aa20fb5cdfed65.png',
            'e628c7_07cf95bd81454c6eae6280f5f0588d65.png',
            'e628c7_4ad5a2225b6a4fee8412ef8a83b9264a.png',
            'e628c7_b2d915048f0e46539e25259ec300b0fa.png',
            'e628c7_6cb1dc2bcca340e787bc849cc4fcae9b.png',
            'e628c7_962c7ada051348e88e16c9073303460d.png',
        ];

        return array_map(
            static fn (string $file): array => ['file' => $file, 'alt' => 'Client and partner logo'],
            $files,
        );
    }
}
