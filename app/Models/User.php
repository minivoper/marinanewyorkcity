<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /*
     * Sanctum's trait, and deliberately NOT Passport's as well, even though
     * this site runs both MCP credential lanes.
     *
     * The two `HasApiTokens` traits collide on six methods and on the
     * `$accessToken` property itself — Sanctum declares it untyped, Passport
     * types it `?ScopeAuthorizable` — so a class that uses both is a PHP fatal
     * error, not a merge. One of them has to win.
     *
     * Sanctum's wins because it is the only one that gives up nothing:
     *
     *  - `cms:token` mints through Sanctum's three-argument `createToken()` and
     *    lists through Sanctum's `tokens()` relation; Passport's signatures are
     *    different and would break the command outright.
     *  - Passport's `TokenGuard` only ever calls `withAccessToken()` on the
     *    model and hands it a `Laravel\Passport\AccessToken`. Sanctum's setter
     *    is untyped, stores it, and `tokenCan()` asks it `can()` — which
     *    `AccessToken` implements. So an OAuth token still narrows its owner,
     *    which is the whole point of putting a trait here.
     *  - Nothing in Passport's authorize/approve/token path reads the trait:
     *    the consent screen goes through `$client->tokens()`, and
     *    `getProviderName()` is read off the *provider*, not the user.
     *
     * What is given up is Passport's own `oauthApps()`/`clients()` accessors,
     * which this site has no screen for — see docs/mcp-oauth.md.
     */

    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
