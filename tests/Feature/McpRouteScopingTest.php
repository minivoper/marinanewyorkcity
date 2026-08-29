<?php

namespace Tests\Feature;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * This application answers on two kinds of host from one route table: the
 * public site at marinanewyorkcity.com, and the admin host that carries the
 * CMS, the MCP endpoint and — now that Marina's ChatGPT connector needs one —
 * a token-issuing OAuth server.
 *
 * The package scopes all of that inside `Route::domain(...)` groups, and the
 * package has its own test for it. This one is marina's, because the property
 * is about THIS application's route table: a route file the package scopes
 * correctly can still be undone here by a stray registration, a published
 * config that switches a lane off so Passport's own ten unscoped routes come
 * back, or an upgrade that changes what `Mcp::web()` returns.
 *
 * The two shapes that put an endpoint on the public site by accident:
 *
 *  - `Mcp::web()` registers GET and DELETE 405-responders BEFORE the POST route
 *    and returns only the POST. Chaining `->domain()` onto it therefore
 *    constrains one route and leaves two answering everywhere.
 *  - `Passport::ignoreRoutes()` is what keeps Passport's own `oauth/authorize`
 *    and `oauth/token` off the public host. It is called from
 *    `CmsServiceProvider::register()` only when the OAuth lane reads as
 *    available, so a config that fails to switch the lane on does not merely
 *    leave OAuth off — it leaves Passport's unscoped routes on.
 *
 * A comment saying so is documentation. This walks the real route table.
 */
class McpRouteScopingTest extends TestCase
{
    private const PUBLIC_HOST = 'http://localhost';

    private const REAL_PUBLIC_HOST = 'http://marinanewyorkcity.com';

    private function adminHost(): string
    {
        return 'http://'.config('cms.admin_domain');
    }

    /**
     * Every route on the two lanes, by URI prefix.
     *
     * @return Collection<int, \Illuminate\Routing\Route>
     */
    private function lanes(): Collection
    {
        return collect(Route::getRoutes()->getRoutes())
            ->filter(function ($route): bool {
                $uri = $route->uri();

                return str_starts_with($uri, 'mcp')
                    || str_starts_with($uri, 'oauth')
                    || str_starts_with($uri, '.well-known');
            });
    }

    public function test_no_mcp_or_oauth_route_is_registered_without_a_domain(): void
    {
        $unscoped = $this->lanes()
            ->filter(fn ($route): bool => $route->getDomain() === null)
            ->map(fn ($route): string => implode('|', $route->methods()).' /'.$route->uri())
            ->values();

        $this->assertSame(
            [],
            $unscoped->all(),
            'These routes answer on every host this application has, including marinanewyorkcity.com.',
        );
    }

    public function test_every_one_of_them_carries_this_site_s_admin_host_specifically(): void
    {
        $admin = (string) config('cms.admin_domain');

        $wrong = $this->lanes()
            ->reject(fn ($route): bool => $route->getDomain() === $admin)
            ->map(fn ($route): string => implode('|', $route->methods()).' /'.$route->uri().' @ '.var_export($route->getDomain(), true))
            ->values();

        $this->assertSame([], $wrong->all(), "Expected every MCP and OAuth route to be scoped to {$admin}.");
    }

    /**
     * The guard above passes trivially against an empty set, and an empty set
     * is exactly what an unset `cms.admin_domain` produces. So name the routes
     * that have to be there.
     */
    public function test_the_lanes_are_actually_registered(): void
    {
        $uris = $this->lanes()->map(fn ($route): string => $route->uri())->unique()->values()->all();

        // The document ChatGPT treats as a MUST: discovery cannot complete
        // without it, and its absence is what kept Marina from adding this
        // site as a custom connector.
        $this->assertContains('.well-known/oauth-protected-resource', $uris);
        $this->assertContains('.well-known/oauth-authorization-server', $uris);
        $this->assertContains('oauth/authorize', $uris);
        $this->assertContains('oauth/token', $uris);
        $this->assertContains('mcp', $uris);
    }

    public function test_the_405_responders_at_the_mcp_endpoint_are_scoped_too(): void
    {
        $domains = collect(Route::getRoutes()->getRoutes())
            ->filter(fn ($route): bool => $route->uri() === 'mcp')
            ->mapWithKeys(fn ($route): array => [implode(',', $route->methods()) => $route->getDomain()]);

        $this->assertGreaterThanOrEqual(3, $domains->count(), 'Expected GET, DELETE and POST routes at /mcp.');

        foreach ($domains as $verbs => $domain) {
            $this->assertSame(config('cms.admin_domain'), $domain, "The {$verbs} route at /mcp is not domain-scoped.");
        }
    }

    public function test_the_discovery_documents_do_not_answer_on_the_public_site(): void
    {
        foreach ([self::PUBLIC_HOST, self::REAL_PUBLIC_HOST] as $host) {
            // Not a 401 and not a 403: no route matches the host at all. These
            // two documents are the one fact the discovery posture exists to
            // withhold from the public site — that this application has an
            // admin surface and where its authorization server is.
            $this->get($host.'/.well-known/oauth-protected-resource')->assertNotFound();
            $this->get($host.'/.well-known/oauth-authorization-server')->assertNotFound();
        }
    }

    public function test_the_authorization_server_does_not_answer_on_the_public_site(): void
    {
        foreach ([self::PUBLIC_HOST, self::REAL_PUBLIC_HOST] as $host) {
            $this->get($host.'/oauth/authorize')->assertNotFound();
            $this->post($host.'/oauth/token')->assertNotFound();
            $this->post($host.'/oauth/register')->assertNotFound();
        }
    }

    public function test_the_mcp_endpoint_does_not_answer_on_the_public_site(): void
    {
        foreach ([self::PUBLIC_HOST, self::REAL_PUBLIC_HOST] as $host) {
            // All three verbs, because the GET and DELETE responders are the
            // ones a chained ->domain() would have left open.
            $this->get($host.'/mcp')->assertNotFound();
            $this->delete($host.'/mcp')->assertNotFound();
            $this->postJson($host.'/mcp', [
                'jsonrpc' => '2.0',
                'id' => 1,
                'method' => 'tools/call',
                'params' => ['name' => 'list_content_types', 'arguments' => []],
            ])->assertNotFound();
        }
    }

    /**
     * The blocker this lane was switched on to clear: the document answered
     * 404 on the admin host, so no connector could finish discovery.
     */
    public function test_the_protected_resource_document_answers_on_the_admin_host(): void
    {
        $issuer = $this->adminHost();

        $this->get($issuer.'/.well-known/oauth-protected-resource')
            ->assertOk()
            ->assertJsonPath('resource', $issuer.'/mcp')
            ->assertJsonPath('authorization_servers.0', $issuer);
    }

    /**
     * RFC 9207 and RFC 8414 both rest on the issuer being one string. Two
     * spellings are two authorization servers to a conforming client, so the
     * value the protected-resource document points at and the value the
     * authorization-server document calls itself have to be identical.
     */
    public function test_the_two_documents_agree_on_one_issuer(): void
    {
        $resource = $this->get($this->adminHost().'/.well-known/oauth-protected-resource')->assertOk();
        $server = $this->get($this->adminHost().'/.well-known/oauth-authorization-server')->assertOk();

        $this->assertSame(
            $resource->json('authorization_servers.0'),
            $server->json('issuer'),
        );

        // And the metadata must not promise what the middleware does not do.
        // `plain` is in the RFC and is not PKCE; `iss` is advertised because
        // AddIssuerToAuthorizationResponse actually emits it.
        $server->assertJsonPath('code_challenge_methods_supported', ['S256']);
        $server->assertJsonPath('authorization_response_iss_parameter_supported', true);
    }

    /**
     * A 200 with a JSON body on a host that must never be findable is exactly
     * the kind of response a crawler keeps.
     */
    public function test_the_discovery_documents_are_not_indexable(): void
    {
        $response = $this->get($this->adminHost().'/.well-known/oauth-protected-resource')->assertOk();

        $this->assertStringContainsString('noindex', (string) $response->headers->get('X-Robots-Tag'));
    }
}
