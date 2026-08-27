<?php

declare(strict_types=1);
use App\Cms\Types\AccessibilityStatementType;
use App\Cms\Types\EventType;
use App\Cms\Types\FreeEventsType;
use App\Cms\Types\HomeType;
use App\Cms\Types\HowICreateType;
use App\Cms\Types\InstagramFeedType;
use App\Cms\Types\MerchType;
use App\Cms\Types\NewsAndPressType;
use App\Cms\Types\PageType;
use App\Cms\Types\PostType;
use App\Cms\Types\ShopType;
use App\Cms\Types\SiteSettingsType;
use App\Cms\Types\TravelUsaType;

return [

    /*
    |--------------------------------------------------------------------------
    | Site identity
    |--------------------------------------------------------------------------
    |
    | The slug registered for this site in the eshlink hub. It is the audience
    | of every service token the hub issues, and it is derived from the token,
    | never accepted from an MCP tool argument.
    |
    */

    'site' => env('CMS_SITE_SLUG'),

    'site_name' => env('CMS_SITE_NAME', env('APP_NAME')),

    'default_locale' => env('CMS_DEFAULT_LOCALE', 'en'),

    /*
    |--------------------------------------------------------------------------
    | Hosts
    |--------------------------------------------------------------------------
    |
    | Every CMS route lives under Route::domain(). When `admin_domain` is null
    | the admin, MCP and service route files are not registered at all, so a
    | site that has not been onboarded yet exposes nothing — public hosts 404
    | every admin path because there is no route to match.
    |
    | Admin hostnames are not secret: TLS certificates appear in public CT logs
    | the moment they are issued. The control is the auth wall, not obscurity.
    |
    */

    'admin_domain' => env('CMS_ADMIN_DOMAIN'),

    /*
    | Path prefix for the admin surface on that host. Empty by default: the
    | admin host serves nothing else, so a prefix would only add typing.
    */

    'admin_prefix' => env('CMS_ADMIN_PREFIX', ''),

    'preview_domain' => env('CMS_PREVIEW_DOMAIN'),

    'production_domain' => env('CMS_PRODUCTION_DOMAIN'),

    /*
    | Explicit host => kind map, consulted by `Eshlink\Cms\Support\HostMode`
    | before the three convenience keys above. Most sites never need this —
    | one host per kind is the common case those three keys cover — but a site
    | with more than one production domain (an apex + a legacy alias, say) or
    | more than one admin alias lists them all here instead of forcing one
    | kind to win.
    |
    |   'example.com' => 'production',
    |   'www.example.com' => 'production',
    |   'admin.example.com' => 'admin',
    */

    'site_domains' => [
        'marinanewyorkcity.com' => 'production',
        'www.marinanewyorkcity.com' => 'production',

        /*
        | A developer machine and the test suite render the production site,
        | so they resolve as production hosts. Without this they would fall to
        | `default_mode` below and suppress their own canonical tags, which
        | would make "does this page still render byte for byte" impossible to
        | answer locally — the one question this integration exists to answer.
        */
        'localhost' => 'production',
        '127.0.0.1' => 'production',

        /*
        | The preview host, named here rather than left to CMS_PREVIEW_DOMAIN
        | so it resolves the same way on a machine with no env for it. It is
        | reachable by anyone holding the link and findable by nobody: the
        | no-index headers and the crawler denylist registered in
        | bootstrap/app.php key off this.
        */
        'marina.eshlink.com' => 'preview',
    ],

    /*
    | Mode for the current host: admin | preview | production.
    |
    | A DB flag cannot gate route *registration* under cached routes, so every
    | route is registered statically and the mode is enforced per request from
    | the request host. Flipping a site to live purges config/route/CDN caches.
    */

    'default_mode' => env('CMS_DEFAULT_MODE', 'preview'),

    /*
    |--------------------------------------------------------------------------
    | Database
    |--------------------------------------------------------------------------
    |
    | Every CMS table is prefixed so the package can be installed into a site
    | that already owns its own schema (Marina's Page/Post/Event/MediaAsset/
    | Setting) with no chance of a collision.
    |
    | Laravel Cloud has an ephemeral filesystem: SQLite is not viable in
    | production. Every site with CMS state runs Postgres.
    |
    */

    'table_prefix' => env('CMS_TABLE_PREFIX', 'cms_'),

    'connection' => env('CMS_DB_CONNECTION'),

    /*
    |--------------------------------------------------------------------------
    | Content types
    |--------------------------------------------------------------------------
    |
    | Each site declares its own types in app/Cms/Types/*.php and lists them
    | here. Defaults on those types are the literals currently hardcoded in
    | Blade, so a migrated site renders byte-identical before anyone logs in.
    |
    */

    'types' => [
        // Marina's own models, wrapped rather than migrated. Her controllers,
        // her `Post::published()` scope, her feeds and her SEO components go on
        // reading these tables exactly as they did before the CMS existed.
        PostType::class,
        EventType::class,
        PageType::class,

        // `App\Cms\Types\MediaAssetType` used to be here, and the screen it
        // drew was "Photo descriptions". It is deliberately not registered any
        // more: the sidebar had it beside "Photos", the two did different jobs
        // under names nobody could tell apart, and the older one contradicted
        // the newer. Marina would upload a photograph with a description on
        // Photos, open Photo descriptions, and be told "No photo descriptions
        // yet" — because that screen reads the legacy `media_assets` index of
        // the files already under `public/media`, which no upload ever writes
        // to. Two screens for one job, one of them denying the work just done.
        //
        // The type class, `App\Models\MediaAsset`, the `media_assets` table
        // and its seeder all stay exactly where they are — nothing is dropped
        // and nothing that renders the public site changes. What is removed is
        // one card in the sidebar, and putting it back is this one line.

        // The `settings` key/value table, as one form rather than five rows of
        // JSON. `App\Cms\Types\SettingsType` used to list those rows here; it
        // is gone, because two things called Settings in one admin is one more
        // than anybody can hold in their head.
        SiteSettingsType::class,

        // The eight pages whose copy used to be literals inside a Blade
        // template, plus the Instagram strip that used to be a disk read.
        // Their defaults ARE those literals, so seeding changes the public
        // HTML by nothing at all.
        HomeType::class,
        NewsAndPressType::class,
        FreeEventsType::class,
        TravelUsaType::class,
        HowICreateType::class,
        ShopType::class,
        MerchType::class,
        AccessibilityStatementType::class,
        InstagramFeedType::class,
    ],

    /*
    | The singleton type the admin's Settings screen edits — jacknova's
    | `config/site.php` becomes one of these. It is an ordinary content type
    | with an ordinary schema; only the place it appears in the navigation is
    | different, because "site settings" is not something an owner looks for
    | under a list of content.
    */

    'settings_type' => env('CMS_SETTINGS_TYPE', 'site_settings'),

    /*
    | Site-wide content rules, applied in addition to the rules a field
    | declares via `governedBy()` and the ones a type returns from `rules()`.
    |
    | Each entry is a `ContentRule` or a `RuleSet` bundling several — a site's
    | whole editorial policy in one class. They are resolved once, into the
    | shared `Eshlink\Cms\Validation\Validator`, because a rule the admin form
    | enforced but an MCP tool did not would be no rule at all.
    */

    'rules' => [
        // App\Cms\Rules\MarinaRuleSet::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Middleware
    |--------------------------------------------------------------------------
    |
    | Stacks applied to the three route files. `cms.no-index` belongs on admin
    | and preview hosts and is additionally applied from the exception handler,
    | so error responses carry the same headers as successful ones.
    |
    */

    'middleware' => [

        /*
        | Applied to the whole admin route file, login screen included. The
        | session guard and the two-factor gate are applied inside the file
        | rather than here: `/login` and the TOTP challenge have to be
        | reachable by someone who is, by definition, not through them yet.
        */

        'admin' => [
            'web',
            'cms.admin-host',
            'cms.no-index',
            'cms.security-headers',
        ],

        'preview' => [
            'web',
            'cms.preview-redirect',
            'cms.no-index',
            'cms.block-crawlers',
            'cms.preview-gate',
        ],

        'mcp' => [
            // Phase 2 ships Sanctum PATs; Phase 4 swaps to Passport OAuth.
            // 'auth:sanctum', 'abilities:mcp:use', 'cms.mcp-gate', 'throttle:cms-mcp',
        ],

        /*
        | The hub service API, including `/_cms/health`. Admin host only, and
        | behind CMS_SERVICE_TOKEN: a liveness endpoint that answers an
        | unauthenticated caller tells anyone who asks that this host runs the
        | CMS. With the token unset the whole surface 404s, which is the right
        | state while the hub does not drive this site yet.
        |
        | Phase 4 swaps the shared secret for the Ed25519 JWT the hub mints —
        | aud=<site>, act=<human>, 60s expiry.
        */

        'service' => [
            'cms.admin-host',
            'cms.service-token',
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Admin security headers
    |--------------------------------------------------------------------------
    |
    | The nonce-based CSP `AdminSecurityHeaders` builds. No `unsafe-inline` —
    | that is the entire reason the nonce exists — so `img_src`/`connect_src`
    | must list every origin the admin UI legitimately loads from (the media
    | disk's public URL, notably) or those requests are refused, not degraded.
    |
    | Shipped as `Content-Security-Policy-Report-Only` until a site confirms
    | zero violations, then flipped to enforcing.
    |
    */

    'security_headers' => [

        'csp' => [
            'enforce' => env('CMS_CSP_ENFORCE', false),
            'img_src' => array_filter(explode(',', (string) env('CMS_CSP_IMG_SRC', ''))),
            'connect_src' => array_filter(explode(',', (string) env('CMS_CSP_CONNECT_SRC', ''))),
            'font_src' => array_filter(explode(',', (string) env('CMS_CSP_FONT_SRC', ''))),
        ],

        'hsts' => [
            'enabled' => env('CMS_HSTS_ENABLED', false),
            'max_age' => (int) env('CMS_HSTS_MAX_AGE', 31536000),
            'include_sub_domains' => env('CMS_HSTS_INCLUDE_SUBDOMAINS', true),
            // Preload is very hard to undo; leave off until every host on the
            // domain is confirmed HTTPS-only.
            'preload' => env('CMS_HSTS_PRELOAD', false),
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Crawler blocking (preview + admin hosts)
    |--------------------------------------------------------------------------
    |
    | Harvested from davidkober's hand-rolled denylist, but split. His list also
    | blocked `curl`, empty user agents, `claudebot` and `anthropic-ai`, which
    | would 403 our own verification matrix and every MCP client we support.
    | This list is crawlers and AI training bots only.
    |
    | A user agent is trivially spoofed: this is a discovery control, never an
    | access control.
    |
    */

    'block_crawlers' => env('CMS_BLOCK_CRAWLERS', true),

    'preview_denylist' => [
        'googlebot', 'bingbot', 'msnbot', 'slurp', 'duckduckbot', 'baiduspider',
        'yandexbot', 'sogou', 'exabot', 'facebot', 'facebookexternalhit',
        'twitterbot', 'linkedinbot', 'pinterestbot', 'applebot', 'ia_archiver',
        'ahrefsbot', 'semrushbot', 'mj12bot', 'dotbot', 'seznambot', 'petalbot',
        'gptbot', 'oai-searchbot', 'ccbot', 'perplexitybot', 'youbot',
        'diffbot', 'bytespider', 'amazonbot', 'omgili', 'timpibot', 'imagesiftbot',
        'google-extended', 'applebot-extended', 'meta-externalagent',
    ],

    /*
    | Never filtered, on any host. MCP clients are the point; /up must not
    | depend on a UA allowlist; OAuth discovery and ACME must stay reachable.
    */

    'crawler_exempt_paths' => [
        'mcp', 'mcp/*', 'up', 'oauth/*', '.well-known/*',
    ],

    /*
    |--------------------------------------------------------------------------
    | Preview gate
    |--------------------------------------------------------------------------
    |
    | Optional passcode on preview hosts; mandatory for davidkober until NY Life
    | compliance clears. A bcrypt hash with NO fallback — the predecessor
    | committed a live credential as an env() default. Fail closed.
    |
    */

    'preview_gate' => [
        'enabled' => env('CMS_PREVIEW_GATE_ENABLED', false),
        'password_hash' => env('CMS_PREVIEW_PASSWORD_HASH'),
        'session_key' => 'cms.preview_gate',
        'ttl_minutes' => 60 * 24 * 7,
        'throttle' => '5,1', // attempts,minutes per IP
    ],

    /*
    | Preview-to-live redirects, off by default. A site turns this on once its
    | real domain is attached: from then on an unauthenticated, non-preview-
    | link GET to the preview host 301s to `production_domain`. Signed preview
    | links, an authenticated CMS session, and the exempt paths below are
    | never redirected, so owners keep previewing drafts on the preview host
    | after launch.
    */

    'preview_redirect' => [
        'enabled' => env('CMS_PREVIEW_REDIRECT_ENABLED', false),
        'exempt_paths' => [
            'up', '.well-known/*', 'mcp', 'mcp/*', 'oauth/*',
            'robots.txt', 'sitemap.xml', 'llms.txt', 'llms-full.txt', 'humans.txt', 'feed.xml',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Admin session auth
    |--------------------------------------------------------------------------
    |
    | The admin UI signs people in against the site's OWN users table through
    | the site's own guard. That is the Phase 1 shape: the hub is not the
    | identity provider yet, and Marina must not wait for it to edit her site.
    | When the hub lands, this guard is what the SSO callback populates — the
    | screens, roles and gates below do not change.
    |
    | `role_column` is added to the site's users table by this package's
    | migration, alongside `hub_user_id` and `disabled_at`.
    |
    */

    'auth' => [

        'guard' => env('CMS_AUTH_GUARD', 'web'),

        'users_table' => env('CMS_USERS_TABLE', 'users'),

        'username' => env('CMS_AUTH_USERNAME', 'email'),

        'role_column' => 'role',

        /*
        | The role an account with no role of its own gets. `viewer` reads and
        | writes nothing, which is the right thing for a site's pre-existing
        | user rows to become the moment the CMS is installed.
        */

        'default_role' => env('CMS_DEFAULT_ROLE', 'viewer'),

        'redirect_after_login' => 'cms.dashboard',

    ],

    /*
    |--------------------------------------------------------------------------
    | Site-local two-factor (TOTP)
    |--------------------------------------------------------------------------
    |
    | Mandatory: every account enrols on first login, before it can reach any
    | screen. The plan is explicit that Marina must not hold publish rights on
    | password-only auth, and an enrolment people can postpone is one nobody
    | completes — so the setup screen IS the first screen.
    |
    | Secrets are encrypted with the app key. Recovery codes are stored as
    | sha256 digests: they are 60-bit random strings we generate ourselves, so
    | a slow hash buys nothing a password hash would, and they are single-use.
    |
    */

    'two_factor' => [

        'issuer' => env('CMS_2FA_ISSUER', env('CMS_SITE_NAME', env('APP_NAME', 'eshlink'))),

        // Periods of 30s either side of now that a code stays valid for, which
        // is the usual allowance for clock drift on a phone.
        'window' => 1,

        'recovery_codes' => 8,

    ],

    /*
    |--------------------------------------------------------------------------
    | Abilities and roles
    |--------------------------------------------------------------------------
    |
    | One vocabulary, three consumers: session guards, MCP tokens and hub
    | service tokens. A tool maps to an ability and is rejected before handle().
    |
    */

    'abilities' => [
        'content.read', 'content.write', 'content.publish', 'content.delete', 'content.reorder',
        'media.read', 'media.upload', 'media.write',
        'settings.write',
        'audit.read',
        'users.manage',
    ],

    'roles' => [

        'viewer' => ['content.read', 'media.read'],

        // Editors save drafts. They never publish.
        'editor' => ['content.read', 'content.write', 'media.read', 'media.upload', 'media.write'],

        'owner' => [
            'content.read', 'content.write', 'content.publish', 'content.delete', 'content.reorder',
            'media.read', 'media.upload', 'media.write', 'settings.write', 'audit.read',
        ],

        'superadmin' => ['*'],

    ],

    /*
    | Approval gate. When true, publish requires a superadmin regardless of the
    | site owner's role — davidkober's state during the compliance lock.
    */

    'requires_approval' => env('CMS_REQUIRES_APPROVAL', false),

    /*
    | Two-factor is mandatory for any publish-capable account. Marina must not
    | receive publish rights on password-only auth, so site-local TOTP ships in
    | Phase 1, before the hub IdP exists.
    */

    'require_two_factor_for_publish' => env('CMS_REQUIRE_2FA_PUBLISH', true),

    /*
    | Local login used only when the hub is down. Off by default; every use
    | raises an alert.
    */

    'breakglass_enabled' => env('CMS_BREAKGLASS_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | MCP
    |--------------------------------------------------------------------------
    |
    | Never accept a site identifier from a tool argument — derive it from the
    | validated token audience and the endpoint. Publishing carries its own
    | scope and an idempotency key so a client retry is not a second publish.
    |
    */

    'mcp' => [

        'enabled' => env('CMS_MCP_ENABLED', false),

        'path' => env('CMS_MCP_PATH', 'mcp'),

        'server' => null, // App\Mcp\SiteContentServer::class

        'guard' => env('CMS_MCP_GUARD', 'sanctum'),

        /*
        | Tool => required ability. McpAbilityGate rejects before handle().
        */

        'tool_abilities' => [
            'list_content_types' => 'content.read',
            'describe_schema' => 'content.read',
            'list_entries' => 'content.read',
            'get_entry' => 'content.read',
            'create_entry' => 'content.write',
            'update_entry' => 'content.write',
            'publish_entry' => 'content.publish',
            'unpublish_entry' => 'content.publish',
            'delete_entry' => 'content.delete',
            'reorder_entries' => 'content.reorder',
            'revert_entry' => 'content.write',
            'request_image_upload' => 'media.upload',
            'list_media' => 'media.read',
            'set_media_alt' => 'media.write',
            'get_audit_history' => 'audit.read',
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Hub (eshlink mission control)
    |--------------------------------------------------------------------------
    |
    | The hub stores no content. It issues Ed25519-signed service tokens; sites
    | hold only the public key, so a compromised site cannot forge a token for
    | a sibling. The audit mirror carries metadata and digests only.
    |
    */

    /*
    | Shared secret for the service API until the Ed25519 tokens land. No
    | fallback, deliberately: an unset value disables the surface (404) rather
    | than leaving it open on a default nobody rotated.
    */

    'service_token' => env('CMS_SERVICE_TOKEN'),

    'hub' => [
        'url' => env('CMS_HUB_URL'),
        'public_keys' => env('CMS_HUB_PUBLIC_KEYS'), // kid => base64 key, JSON
        'audit_mirror' => env('CMS_HUB_AUDIT_MIRROR', false),
        'token_ttl_seconds' => 60,
    ],

    /*
    |--------------------------------------------------------------------------
    | Media
    |--------------------------------------------------------------------------
    |
    | S3 only: Laravel Cloud's filesystem is ephemeral. Uploads land in the
    | quarantine disk, are checked (finfo MIME allowlist, byte cap, pixel cap
    | against decompression bombs), re-encoded through Intervention with EXIF
    | stripped, then promoted to the public disk under a content-addressed name.
    | SVG is rejected outright rather than sanitised.
    |
    */

    'media' => [

        'disk' => env('CMS_MEDIA_DISK', 's3'),

        'quarantine_disk' => env('CMS_MEDIA_QUARANTINE_DISK', 's3-quarantine'),

        // Key prefix for every stored object. Paths under it are content
        // addressed: `media/ab/cd/<sha256>.jpg`.
        'path_prefix' => env('CMS_MEDIA_PATH_PREFIX', 'media'),

        // Intervention driver: gd (the extension Cloud's PHP image ships) or
        // imagick. Imagick handles more formats and animated GIFs better.
        'driver' => env('CMS_MEDIA_DRIVER', 'gd'),

        'quality' => (int) env('CMS_MEDIA_QUALITY', 82),

        'max_bytes' => 20 * 1024 * 1024,

        'max_pixels' => 40_000_000,

        'mimes' => ['image/jpeg', 'image/png', 'image/webp', 'image/avif', 'image/gif'],

        'variants' => [
            'thumb' => ['width' => 400, 'format' => 'webp'],
            'medium' => ['width' => 1200, 'format' => 'webp'],
            'large' => ['width' => 2400, 'format' => 'webp'],
        ],

        'ticket_ttl_minutes' => 60,

        'require_alt' => true,

    ],

    /*
    |--------------------------------------------------------------------------
    | Rate limits (per minute unless stated)
    |--------------------------------------------------------------------------
    */

    'rate_limits' => [
        'login' => '5,1',        // per IP
        'mcp' => '60,1',         // per token
        'publish' => '10,1',     // per token
        'upload_tickets' => 20,  // per hour
        'service' => '300,1',    // per site
    ],

    /*
    |--------------------------------------------------------------------------
    | SEO / GEO surface
    |--------------------------------------------------------------------------
    |
    | Generated from published content on production hosts, and 404 everywhere
    | else. On preview hosts canonical tags and og:url are suppressed entirely:
    | a canonical pointing at the real domain is exactly how a preview host
    | teaches a crawler that it exists.
    |
    */

    'seo' => [
        'sitemap' => true,
        'feeds' => true,
        'json_ld' => true,
        'redirects' => true,
        'canonical_on_preview' => false,

        // Extra `Disallow:` lines for the production robots.txt, beyond the
        // admin/preview hosts (which are already a separate host entirely).
        'robots_disallow' => [],

        // Path prefix per content-type key for the sitemap/feed/llms.txt URL
        // builder, e.g. ['post' => '/blog']. A singleton with no entry here
        // resolves to '/'; a collection type defaults to '/{type key}'. A
        // site whose routes do not match that shape declares it here rather
        // than the package guessing wrong silently.
        'url_prefixes' => [],

        // Field names tried, in order, for a feed/llms.txt entry's summary.
        'summary_fields' => ['excerpt', 'summary', 'description', 'body', 'content'],

        'feed_limit' => 50,
    ],

    'geo' => [
        'llms_txt' => true,
        'llms_full_txt' => true,
        'markdown_mirrors' => true,
        'humans_txt' => true,
        'profile_json' => true,
    ],

    /*
    | Credited in humans.txt, e.g. ['developer' => 'Evgeny Mironov'].
    */

    'humans' => [],

    /*
    |--------------------------------------------------------------------------
    | Read-path cache
    |--------------------------------------------------------------------------
    |
    | TTL for `Eshlink\Cms\Support\CmsReader` — the read path behind the `@cms`
    | Blade directive and the `Cms` facade. Short by design: publishing does
    | not proactively bust this cache, so the TTL alone bounds how long an
    | edit takes to appear. Set to 0 to disable caching outright (useful while
    | developing a type).
    */

    'cache' => [
        'ttl_seconds' => (int) env('CMS_READ_CACHE_TTL', 60),
    ],

    /*
    |--------------------------------------------------------------------------
    | Audit
    |--------------------------------------------------------------------------
    |
    | Append-only. In production the application's database role is granted
    | INSERT and SELECT only on the audit table; the AppendOnly trait is the
    | local half of the same guarantee.
    |
    */

    'audit' => [
        'enabled' => true,
        'field_level_diffs' => true,
        'export_disk' => env('CMS_AUDIT_EXPORT_DISK', 's3-backups'),
        'retention_days' => null, // null = keep forever
    ],

    /*
    |--------------------------------------------------------------------------
    | Publishing
    |--------------------------------------------------------------------------
    */

    'publishing' => [
        'idempotency_ttl_minutes' => 60,
        'scheduled' => true,     // needs the cms:publish-scheduled scheduler entry
        'purge_cache_on_publish' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Install
    |--------------------------------------------------------------------------
    |
    | Seeding defaults is first-run-only, guarded by a bootstrap flag in
    | cms_settings. Marina's Cloud deploy command runs `migrate --force --seed`
    | and her seeders updateOrCreate content — without this guard every deploy
    | would silently revert her edits.
    |
    */

    'install' => [
        'seed_defaults' => true,
        'guard_flag' => 'cms.installed_at',
    ],

];
