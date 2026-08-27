# BUILD.md — marina.newyorkcity

## What this is

The personal website of **Marina Kapler**, the NYC content creator behind
[@marina.newyorkcity](https://www.instagram.com/marina.newyorkcity) — an independent
New York City media brand (news, guides, free events, cinematic iPhone videography,
brand collaborations). This repo is a **Laravel rebuild of her Wix Studio draft site**,
recreated page-for-page so the content can live on her own domain instead of Wix.

## Source of truth

- Wix Studio draft: https://soulbygirl.wixstudio.com/marinanews?rc=test-site
- Harvested reference material lives in `docs/wix-ref/`:
  - `home-copy.md` — exact homepage text in DOM order
  - `tokens.md` — design tokens (fonts, type scale, colors) extracted from the served Wix theme CSS
  - `sitemap-harvest.json` — 73 URLs crawled from the Wix site
  - `instagram.json` — snapshot of the Instagram account used for the home-page grid

## URLs

| What | URL |
|---|---|
| Local dev | http://127.0.0.1:8001 (`php artisan serve --port=8001`) |
| Live (Laravel Cloud) | https://marinanewyorkcity-production-x1iuqe.laravel.cloud |
| Intended canonical | https://marinanewyorkcity.com — **domain still parked on Canva, not yet pointed at Cloud** |
| GitHub | https://github.com/minivoper/marinanewyorkcity |

## Stack

- Laravel 13 (framework 13.29), PHP 8.3+
- Blade templates + one hand-written CSS file (`resources/css/app.css`) bundled with Vite — no JS framework, no Tailwind
- SQLite locally, Postgres (Neon) on Laravel Cloud
- No auth/login — public content site only. Content is database-seeded, not admin-managed.

## Data model

Models: `Post` (blog/news/guide, with `schema_type`), `Event` + `EventOccurrence`,
`Page` (legal/static pages), `Setting`, `MediaAsset`. Seeders in `database/seeders/`
hold the actual content (11 posts, events with occurrences, 5+ static pages, site
settings); `DatabaseSeeder` runs `PostSeeder`, `EventSeeder`, `PageSeeder`, `SettingSeeder`.

## Pages & routes (`routes/web.php`)

- `/` home — hero, news, guides, about, Instagram grid
- `/blog`, `/news`, `/guides`, `/post/{slug}` — posts by type
- `/event-list`, `/event-details/{slug}` — events (occurrence slugs resolve too)
- `/news-and-press`, `/travel-usa`, `/how-i-create`, `/shop`, `/merch`, `/free-events`
- `/contact` (GET + POST), `/search`
- Legal: `/privacy-policy`, `/terms-and-conditions`, `/about`, `/work-with-me`, `/press`, `/accessibility-statement`
- SEO: `/sitemap.xml`, `/robots.txt`, `/llms.txt` (aliased at `/ai.txt`), `/feed.xml`, `/feed.json`
- 301s from Wix's `/blank` … `/blank-7` placeholder URLs to their real pages

Feature tests in `tests/Feature/` (HomeTest, PostTest, EventTest, SeoTest, SitemapTest, WixPagesTest) cover these routes. Run with `php artisan test --compact`.

## How content was harvested

The Wix draft was crawled (73 URLs → `docs/wix-ref/sitemap-harvest.json`); page copy and
theme CSS tokens were transcribed into `docs/wix-ref/`. All images were downloaded
locally into `public/media/` (`brand/`, `about/`, `posts/`, `events/`, `shop/`, `travel/`,
`instagram/` — the Instagram folder holds ~25 snapshot JPGs of recent posts). The three
Wix-hosted webfonts (Arial Black, Avenir LT 35 Light, DIN Next Light) were saved as
woff2 in `public/fonts/` and preloaded in the layout. Most harvested media is local;
the remaining Wix CDN images embedded inside imported HTML are tracked below.

## Design notes

- Black background with gold accent (`--color-gold: #c9b445` in `app.css`), matching the Wix screenshot: full-bleed hero with "new york / where magic begins" in Arial Black
- Circular logo (`public/media/brand/logo.png`, cropped round) in header and footer
- Single shared layout `resources/views/layouts/app.blade.php`: transparent-over-hero header option, footer with Explore / Useful links / Social columns
- Type scale mirrors the Wix theme tokens documented in `docs/wix-ref/tokens.md`

## SEO / GEO

- `config/site.php` holds `production_url` (https://marinanewyorkcity.com), author Marina Kapler, email, social links — sitemap/feeds/llms.txt build canonical URLs from it
- JSON-LD via `App\View\Components\JsonLd`: `Person` (Marina Kapler, homeLocation NYC), `NewsMediaOrganization`, `NewsArticle`/`Article` per post, `Event` per event
- `<x-seo-head>` component sets title/description/OG/canonical per page
- `robots.txt` explicitly **allows** AI crawlers (GPTBot, ChatGPT-User, PerplexityBot, ClaudeBot, Google-Extended, Applebot-Extended, Bytespider) plus Googlebot
- `/llms.txt` and `/ai.txt` serve a plain-text brand summary for LLM crawlers
- RSS (`/feed.xml`) and JSON Feed (`/feed.json`)

## Run locally

```bash
composer install
npm install
cp .env.example .env && php artisan key:generate   # if no .env yet
touch database/database.sqlite                     # DB_CONNECTION=sqlite
php artisan migrate --seed
npm run build          # or: npm run dev / composer run dev
php artisan serve --port=8001
```

Then open http://127.0.0.1:8001.

## Deployment

- Pushed to GitHub as `minivoper/marinanewyorkcity` (created via `gh`)
- Laravel Cloud: org **Evgeny Mir**, app **marinanewyorkcity**, production environment backed by a Neon Postgres cluster named **marina-db**
- Deploy command: `php artisan migrate --force --seed` (seeders are idempotent enough to re-run; content ships via seeders, so redeploying refreshes content)
- Deploys triggered from Laravel Cloud on push / via the Cloud dashboard ("ship")

### Before any production import: check who can sign in

The dev database has carried test accounts more than once. They are ordinary
`users` rows with confirmed two-factor secrets, so they work exactly as well in
production as they do here, and nothing in the deploy will notice them.

Run this against the target database before importing anything into it, and
again afterwards:

```sh
php artisan tinker --execute 'App\Models\User::query()->pluck("email", "id")->each(fn ($e, $i) => print("$i  $e\n"));'
```

Marina's own account is the only row that belongs there. Anything else,
including anything at `@eshlink.test`, is a test account and must be removed
along with its two-factor row:

```sh
php artisan tinker --execute '
$user = App\Models\User::where("email", "someone@eshlink.test")->first();
DB::table("cms_two_factor")->where("user_id", $user->id)->delete();
$user->delete();
'
```

Two accounts, `qa@eshlink.test` and `qa-editor@eshlink.test`, were created for
the pre-deploy QA passes and have since been removed from the dev database.
Their audit rows stay, because the activity log is append-only by design, and a
signed-in account that no longer exists is exactly what that log is for.

### Media in production

The photo library serves its files one of two ways, and the disk decides which
(see `Eshlink\Cms\Media\MediaService::urlForPath()`):

- a disk that can address itself publicly hands out its own URL, and the bytes
  never touch PHP. That is what production must use: set `CMS_MEDIA_DISK=s3`
  with `AWS_URL` pointing at the bucket or the CDN in front of it, and make sure
  objects are publicly readable.
- anything else is served by the package's own `/cms-media/{path}` route. That
  is the local default, and it is correct but slower.

The failure mode this replaced is worth knowing: a `local` disk with
`'serve' => true` and no `visibility` returns a plausible `/storage/...` URL for
a private disk, and every image on the site is a 404. If photographs stop
appearing after a deploy, that is the first thing to check.

## Unfinished

1. **DNS**: marinanewyorkcity.com is still attached to Canva. Point it at Laravel Cloud (add the custom domain in the Cloud dashboard, update DNS), after which the canonical URLs in sitemap/feeds/llms.txt become correct.
2. **Visual polish**: layout is close to the Wix draft but not pixel-identical — spacing, hover states, and some section compositions could be tightened against the Wix reference.
3. **Media migration**: move the remaining images embedded in imported post/page HTML off `static.wixstatic.com` and into the local media pipeline, then replace their hot-linked URLs and add meaningful alt text. The links still render today, so this is tracked here rather than changed as part of the CMS bug fixes.
