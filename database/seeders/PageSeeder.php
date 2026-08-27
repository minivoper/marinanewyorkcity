<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class PageSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->pages() as $page) {
            $page['body'] = $this->bodyFor($page['slug'], $page['body']);

            Page::query()->updateOrCreate(['slug' => $page['slug']], $page);
        }
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function pages(): array
    {
        return [
            [
                'slug' => 'privacy-policy',
                'title' => 'Privacy Policy',
                'body' => <<<'HTML'
<p><strong>Effective Date: May 27, 2026</strong></p>
<p>This Privacy Policy explains how Marina New York City handles information submitted through marinanewyorkcity.com. Information you choose to provide, such as an email sent to us, is used to respond to your request and operate the website.</p>
<p>The site may use standard hosting logs and analytics to understand visits, maintain security, and improve content. Third-party sites linked from this website have their own privacy practices.</p>
<p>For privacy questions or requests, contact info@marinanewyorkcity.com.</p>
HTML,
                'meta_title' => 'Privacy Policy | marina.newyorkcity',
                'meta_description' => 'Privacy policy for Marina New York City and marinanewyorkcity.com, effective May 27, 2026.',
            ],
            [
                'slug' => 'terms-and-conditions',
                'title' => 'Terms and Conditions',
                'body' => <<<'HTML'
<p><strong>Effective Date: May 27, 2026</strong></p>
<p>These Terms and Conditions govern use of Marina New York City and marinanewyorkcity.com. Content is provided for general informational purposes and event details can change after publication.</p>
<p>Unless otherwise stated, the site’s original writing, video, and photography may not be reproduced, licensed, or used commercially without permission. Links to third-party services do not constitute control of or responsibility for those services.</p>
<p>For permissions, licensing, or questions about these terms, contact info@marinanewyorkcity.com.</p>
HTML,
                'meta_title' => 'Terms and Conditions | marina.newyorkcity',
                'meta_description' => 'Terms and conditions for Marina New York City and marinanewyorkcity.com, effective May 27, 2026.',
            ],
            [
                'slug' => 'about',
                'title' => 'About Marina Kapler',
                'body' => <<<'HTML'
<p>Marina Kapler is the creator behind @marina.newyorkcity: a New York City content creator, marketing professional, and visual storyteller known for cinematic iPhone videography.</p>
<p>Her Reels have reached more than 800,000 views, and her content reaches more than 5 million monthly views. Marina’s approach is simple: “My content doesn't look like advertising. It looks like New York.”</p>
<h2>Frequently Asked Questions</h2>
<h3>Who is Marina Kapler?</h3>
<p>Marina Kapler is a New York City content creator, marketing professional, and cinematic visual storyteller.</p>
<h3>What does marina.newyorkcity cover?</h3>
<p>The brand covers New York City culture, events, neighborhoods, lifestyle, technology, guides, and cinematic city moments.</p>
<h3>How can I license Marina’s footage?</h3>
<p>For footage licensing, partnerships, and press inquiries, email info@marinanewyorkcity.com.</p>
HTML,
                'meta_title' => 'About Marina Kapler | NYC Content Creator',
                'meta_description' => 'Meet Marina Kapler, the New York City creator and visual storyteller behind @marina.newyorkcity.',
            ],
            [
                'slug' => 'work-with-me',
                'title' => 'Work with Marina Kapler',
                'body' => <<<'HTML'
<p>Marina partners with brands on cinematic storytelling built around New York City, luxury lifestyle, and thoughtful technology integration.</p>
<h2>Creative partnerships</h2>
<p>Projects can include concept development, iPhone-first production, short-form video, photography, and platform-native campaign assets with a distinctive NYC focus.</p>
<h2>Production add-ons</h2>
<p>Drone footage, original or licensed music, and expanded production teams are available as project add-ons when the concept and location allow them.</p>
<h2>Footage licensing</h2>
<p>Existing New York City footage may be licensed for editorial or commercial use. Contact info@marinanewyorkcity.com with the project, channels, territory, and desired usage period.</p>
HTML,
                'meta_title' => 'Work with Marina Kapler | NYC Brand Storytelling',
                'meta_description' => 'Partner with Marina Kapler for cinematic NYC storytelling, luxury and tech campaigns, production add-ons, and footage licensing.',
            ],
            [
                'slug' => 'press',
                'title' => 'Press and Media',
                'body' => <<<'HTML'
<p>Marina Kapler is the New York City content creator, marketing professional, and visual storyteller behind marina.newyorkcity and @marina.newyorkcity.</p>
<p>Her work focuses on cinematic iPhone videography, NYC culture, events, luxury lifestyle, and technology. Reels have reached more than 800,000 views, with more than 5 million monthly views across her content.</p>
<h2>Official links</h2>
<ul>
<li><a href="https://www.instagram.com/marina.newyorkcity/">Instagram</a></li>
<li><a href="https://www.tiktok.com/@marina.newyorkcity">TikTok</a></li>
<li><a href="https://www.threads.com/@marina.newyorkcity">Threads</a></li>
<li><a href="https://www.facebook.com/marina.nycity">Facebook</a></li>
<li><a href="https://kit.marinanewyorkcity.com">Media kit</a></li>
<li><a href="https://links.marinanewyorkcity.com">Official links</a></li>
</ul>
<p>For interviews, media requests, or licensing, email info@marinanewyorkcity.com.</p>
HTML,
                'meta_title' => 'Press | Marina Kapler and marina.newyorkcity',
                'meta_description' => 'Press facts, official profiles, and media contact information for Marina Kapler and marina.newyorkcity.',
            ],
        ];
    }

    private function bodyFor(string $slug, string $fallback): string
    {
        $path = database_path("data/pages/{$slug}.html");

        return File::exists($path) ? File::get($path) : $fallback;
    }
}
