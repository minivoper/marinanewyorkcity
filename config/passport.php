<?php

declare(strict_types=1);

/**
 * Rebuild a PEM key that has travelled through an environment variable.
 *
 * Laravel Cloud writes every environment variable into a generated `.env`
 * file, and a `.env` line cannot hold a raw newline. Setting the key through
 * the CLI proves it: a 812-byte public key came back 799 bytes, which is
 * exactly its thirteen newlines removed, leaving a single line that no PEM
 * parser will accept. Passport reads `env('PASSPORT_PRIVATE_KEY')` straight
 * through with no normalisation of its own, so the repair has to happen here.
 *
 * Three shapes are accepted, because all three are things a person will
 * reasonably put in the variable and being strict about it buys nothing:
 *
 *   1. **Base64 of the whole PEM** — the shape to prefer in production. It
 *      contains no newlines at all, so there is nothing for the platform to
 *      strip and no quoting for it to get wrong.
 *   2. **One line with literal `\n` escapes** — what you get from hand-editing
 *      the variable in the dashboard.
 *   3. **A real PEM with real newlines** — what a developer's local `.env` and
 *      `php artisan passport:keys` produce. Left alone.
 *
 * A key that survived intact must come out byte-identical, or local and
 * deployed would be signing with different material.
 */
$pem = static function (?string $value): ?string {
    if ($value === null || trim($value) === '') {
        return null;
    }

    $value = trim($value);

    // Shape 1. Only attempted when the value does not already look like a PEM,
    // so a real key is never fed to base64_decode() on the off chance it
    // decodes to something. Strict mode, and the result has to actually be a
    // PEM — otherwise a mangled key would be silently replaced by rubbish.
    if (! str_contains($value, '-----BEGIN')) {
        $decoded = base64_decode($value, true);

        if (is_string($decoded) && str_contains($decoded, '-----BEGIN')) {
            return rtrim($decoded, "\n")."\n";
        }
    }

    // Shape 2. Guarded on there being no real newlines already: a key that has
    // its newlines does not need this, and a passphrase-free PEM never legally
    // contains a backslash followed by an n.
    if (! str_contains($value, "\n") && str_contains($value, '\n')) {
        $value = str_replace('\n', "\n", $value);
    }

    // Shape 3 falls through to here untouched but for the trailing newline,
    // which OpenSSL writes and which trim() above would otherwise drop.
    return rtrim($value, "\n")."\n";
};

return [

    /*
    |--------------------------------------------------------------------------
    | Passport Guard
    |--------------------------------------------------------------------------
    |
    | Here you may specify which authentication guard Passport will use when
    | authenticating users. This value should correspond with one of your
    | guards that is already present in your "auth" configuration file.
    |
    */

    'guard' => 'web',

    'middleware' => [],

    /*
    |--------------------------------------------------------------------------
    | Encryption Keys
    |--------------------------------------------------------------------------
    |
    | Passport uses encryption keys while generating secure access tokens for
    | your application. Locally these come from storage/oauth-*.key; in
    | production they come from the environment, repaired by $pem above.
    |
    */

    'private_key' => $pem(env('PASSPORT_PRIVATE_KEY')),

    'public_key' => $pem(env('PASSPORT_PUBLIC_KEY')),

    /*
    |--------------------------------------------------------------------------
    | Passport Database Connection
    |--------------------------------------------------------------------------
    |
    | By default, Passport's models will utilize your application's default
    | database connection. If you wish to use a different connection you
    | may specify the configured name of the database connection here.
    |
    */

    'connection' => env('PASSPORT_CONNECTION'),

];
