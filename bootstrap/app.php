<?php

use Eshlink\Cms\CmsServiceProvider;
use Eshlink\Cms\Http\Middleware\BlockCrawlers;
use Eshlink\Cms\Http\Middleware\PreviewGate;
use Eshlink\Cms\Http\Middleware\PreviewToLiveRedirect;
use Eshlink\Cms\Support\AdminErrorPages;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        /*
         * The discovery posture for marina.eshlink.com, which is reachable by
         * anyone holding the link and must be findable by nobody.
         *
         * Every one of these is host-aware through Eshlink\Cms\Support\HostMode
         * and does nothing at all on a production host, so the public site at
         * marinanewyorkcity.com answers exactly as it did before this closure
         * had anything in it — same bytes, same headers. HostModeTest holds
         * that line.
         */

        /*
         * Global rather than grouped, and that is the whole point: global
         * middleware wrap routing itself, so a 404 for a path that matched no
         * route — and a rendered 500 — pass back out through this and carry
         * the headers. Group middleware would miss both, and a 404 without
         * X-Robots-Tag is exactly the response a crawler keeps.
         */
        CmsServiceProvider::forceNoIndexGlobally($middleware);

        /*
         * And the admin's own framing and sniffing headers, global for exactly
         * the same reason. `cms.security-headers` is on the admin route group,
         * so `/login` carried X-Frame-Options and the not-found screen beside
         * it did not — a URI that matched no route never enters a group. The
         * middleware asks HostMode whose host answered before it sets
         * anything, so marinanewyorkcity.com is untouched by this line.
         */
        CmsServiceProvider::secureAdminGlobally($middleware);

        /*
         * On the `web` group because the preview gate reads and writes a
         * session, and because the crawler denylist only has an opinion about
         * pages a person could be shown. The classes are named directly rather
         * than through their `cms.*` aliases: this closure runs before the
         * package's provider has registered them with the router.
         */
        $middleware->appendToGroup('web', [
            PreviewToLiveRedirect::class,
            BlockCrawlers::class,
            PreviewGate::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        /*
         * The admin's own refusal, expiry and not-found screens, on the admin
         * host only.
         *
         * Same shape and the same promise as the no-index registration above:
         * AdminErrorPages asks HostMode which host answered, renders a CMS
         * screen for the admin host and returns null for every other host, so
         * a dead link on marinanewyorkcity.com is still answered by Laravel's
         * own page, byte for byte. AdminErrorPagesTest holds both halves.
         */
        AdminErrorPages::register($exceptions);
    })->create();
