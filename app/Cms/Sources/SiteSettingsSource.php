<?php

namespace App\Cms\Sources;

use App\Models\Setting;
use Eshlink\Cms\Contracts\ContentSource;
use Eshlink\Cms\Contracts\ContentType;
use Eshlink\Cms\Exceptions\StaleRevisionException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * One editable screen over the five rows of Marina's `settings` table.
 *
 * The table is key/value: `site.name` holds `{"value": "..."}`, `site.socials`
 * holds a map of six addresses, `site.emails` holds a one-element list. Shown
 * as content it is five rows of hand-written JSON, which is a format, not a
 * question anybody can answer. Shown through this it is one form with ten
 * labelled boxes, and the JSON never leaves the database.
 *
 * Translation happens here and only here, in both directions, because the
 * stored shape is load-bearing: `site.emails` is a list and `site.email` is a
 * scalar, and whatever reads them must go on getting exactly what it got
 * before. So the fields fan out on write — one "Contact email" box updates both
 * `site.email` and `site.emails` — and any key inside `site.socials` this
 * schema does not name is merged through untouched rather than dropped.
 *
 * There is no draft copy. The `settings` table has no second column to hold one
 * and adding one would put the site's contact address behind a publish step it
 * has never had. So a save is live, {@see publish()} confirms what is already
 * true, and the screen reports itself published rather than promising a step
 * that would do nothing. This is the one content type on the site that works
 * that way, and it works that way because it is not content.
 */
class SiteSettingsSource implements ContentSource
{
    /**
     * The single row's id. Settings are one thing, so it is a constant rather
     * than a database key: there is nothing for an autoincrement to
     * disambiguate, and a stable id keeps `/content/site_settings/{id}` a link
     * that survives a reseed.
     */
    public const ENTRY_ID = 'site_settings';

    /**
     * Fields stored one-per-row as `{"value": "..."}`.
     *
     * @var array<string, string>
     */
    private const SCALARS = [
        'name' => 'site.name',
        'email' => 'site.email',
        'monthly_views' => 'site.monthly_views',
    ];

    /**
     * Fields stored together as the `site.socials` map.
     *
     * @var array<int, string>
     */
    private const SOCIALS = ['instagram', 'tiktok', 'threads', 'facebook', 'kit', 'links'];

    private const SOCIALS_KEY = 'site.socials';

    /**
     * The list form of the contact address. It is a separate row rather than a
     * derived value, so it is written in the same transaction as `site.email`
     * and the two can never disagree.
     */
    private const EMAIL_LIST_KEY = 'site.emails';

    public function find(ContentType $type, string $id): ?array
    {
        return $id === self::ENTRY_ID ? $this->record() : null;
    }

    /**
     * Settings have no address of their own, so there is nothing to look one up
     * by. `EntryService::locate()` reaches a singleton through `all()`.
     */
    public function findBySlug(ContentType $type, string $slug, ?string $locale = null): ?array
    {
        return null;
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function all(ContentType $type, array $filters = []): Collection
    {
        return collect([$this->record()]);
    }

    /**
     * Identical to {@see all()}, and legitimately so: these rows are the live
     * values. The prohibition the contract states — never fall back to a draft
     * column — is kept by there being no draft column to fall back to.
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function published(ContentType $type, array $filters = []): Collection
    {
        return $this->all($type, $filters);
    }

    /**
     * Write the boxes back out to the rows.
     *
     * Partial by contract: a key absent from `$data` leaves its row alone. That
     * is what lets an unknown social network survive a save — and what lets a
     * future field be added without a migration that backfills every row.
     *
     * @param  array<string, mixed>  $data
     * @param  array<string, mixed>  $meta
     * @return array<string, mixed>
     */
    public function saveDraft(ContentType $type, ?string $id, array $data, array $meta = []): array
    {
        if ($id !== null && $id !== self::ENTRY_ID) {
            throw new InvalidArgumentException('There is only one set of site settings.');
        }

        $this->assertFresh($meta);

        return DB::transaction(function () use ($data): array {
            foreach (self::SCALARS as $field => $key) {
                if (array_key_exists($field, $data)) {
                    $this->put($key, ['value' => $this->text($data[$field])]);
                }
            }

            if (array_key_exists('email', $data)) {
                $this->put(self::EMAIL_LIST_KEY, [$this->text($data['email'])]);
            }

            $socials = array_intersect_key($data, array_flip(self::SOCIALS));

            if ($socials !== []) {
                // Merged over what is there, never replacing it. The schema
                // names six networks; the row is allowed to hold seven, and a
                // save through this screen is not the moment to discover that
                // the seventh has quietly disappeared from the footer.
                $this->put(self::SOCIALS_KEY, array_merge(
                    $this->stored(self::SOCIALS_KEY),
                    array_map(fn (mixed $value): string => $this->text($value), $socials),
                ));
            }

            return $this->record();
        });
    }

    /**
     * A confirmation, not a promotion.
     *
     * Saving already changed the live values, so there is nothing to move. The
     * method exists because the publish route is shared by every type and an
     * exception here would turn a harmless button into a 500. The idempotency
     * key is ignored for the same reason a replay is harmless: nothing is
     * written, so nothing can be written twice.
     *
     * @param  array<string, mixed>  $meta
     * @return array<string, mixed>
     */
    public function publish(ContentType $type, string $id, array $meta = [], ?string $idempotencyKey = null): array
    {
        return $this->requireRecord($id);
    }

    /**
     * Nothing to withdraw. A site with no contact address is not a site with
     * one fewer page; it is a contact form that mails nobody. The record comes
     * back unchanged rather than the call failing, because the honest answer to
     * "unpublish the settings" is that it did not happen and nothing broke.
     *
     * @param  array<string, mixed>  $meta
     * @return array<string, mixed>
     */
    public function unpublish(ContentType $type, string $id, array $meta = []): array
    {
        return $this->requireRecord($id);
    }

    /**
     * @param  array<string, mixed>  $meta
     */
    public function delete(ContentType $type, string $id, array $meta = []): bool
    {
        return false;
    }

    /**
     * @param  array<string, mixed>  $meta
     * @return array<string, mixed>
     */
    public function revert(ContentType $type, string $id, int $version, array $meta = []): array
    {
        throw new InvalidArgumentException('Site settings keep no history to revert to.');
    }

    /**
     * Empty, and empty on purpose. Version rows are written by the sources that
     * own a `data`/`published_data` split; these rows have neither, so there is
     * no snapshot to hand back and the history screen correctly shows nothing.
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function versions(ContentType $type, string $id): Collection
    {
        return collect();
    }

    /**
     * @param  array<int, string>  $orderedIds
     * @param  array<string, mixed>  $meta
     */
    public function reorder(ContentType $type, array $orderedIds, array $meta = []): bool
    {
        throw new InvalidArgumentException('The site_settings content type is not orderable.');
    }

    /**
     * The five rows read back as one flat payload.
     *
     * @return array<string, mixed>
     */
    private function record(): array
    {
        $rows = $this->rows();

        $data = [];

        foreach (self::SCALARS as $field => $key) {
            $data[$field] = $this->text($rows[$key]['value'] ?? null);
        }

        $socials = is_array($rows[self::SOCIALS_KEY] ?? null) ? $rows[self::SOCIALS_KEY] : [];

        foreach (self::SOCIALS as $field) {
            $data[$field] = $this->text($socials[$field] ?? null);
        }

        $version = $this->version();

        return [
            'id' => self::ENTRY_ID,
            'type' => 'site_settings',
            'slug' => null,
            'locale' => (string) (config('cms.default_locale') ?? 'en'),
            'status' => 'published',
            'title' => 'Site settings',
            'data' => $data,
            'published_data' => $data,
            'published_at' => $this->touchedAt(),
            'scheduled_at' => null,
            'position' => 0,
            'current_version' => $version,
            'has_unpublished_changes' => false,
            'updated_at' => $this->touchedAt(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function requireRecord(string $id): array
    {
        if ($id !== self::ENTRY_ID) {
            throw new InvalidArgumentException('There is only one set of site settings.');
        }

        return $this->record();
    }

    /**
     * @return array<string, mixed>
     */
    private function rows(): array
    {
        return Setting::query()
            ->whereIn('key', $this->keys())
            ->get()
            ->mapWithKeys(fn (Setting $setting): array => [
                $setting->getAttribute('key') => $setting->getAttribute('value'),
            ])
            ->all();
    }

    /**
     * The value of one row as it is stored, or an empty array if the row has
     * never existed.
     *
     * @return array<string, mixed>
     */
    private function stored(string $key): array
    {
        $value = Setting::query()->where('key', $key)->value('value');

        return is_array($value) ? $value : [];
    }

    /**
     * @param  array<mixed>  $value
     */
    private function put(string $key, array $value): void
    {
        Setting::query()->updateOrCreate(['key' => $key], ['value' => $value]);
    }

    /**
     * @return array<int, string>
     */
    private function keys(): array
    {
        return array_merge(array_values(self::SCALARS), [self::SOCIALS_KEY, self::EMAIL_LIST_KEY]);
    }

    /**
     * The optimistic lock, taken from the rows themselves.
     *
     * There is no version counter to read, so the newest `updated_at` across
     * the five rows stands in for one: any save moves it, so a second editor
     * holding a stale form is refused rather than silently overwriting the
     * first. It is a second-resolution clock, which is the granularity of the
     * column — two saves inside the same second are indistinguishable, and on a
     * screen one person opens a few times a year that is a trade worth making
     * over adding a column to a table other code reads.
     */
    private function version(): int
    {
        return $this->touched()?->getTimestamp() ?? 1;
    }

    private function touchedAt(): ?string
    {
        return $this->touched()?->toIso8601String();
    }

    private function touched(): ?Carbon
    {
        $max = Setting::query()->whereIn('key', $this->keys())->max('updated_at');

        return $max === null ? null : Carbon::parse($max);
    }

    /**
     * @param  array<string, mixed>  $meta
     */
    private function assertFresh(array $meta): void
    {
        if (! array_key_exists('base_revision_id', $meta) || $meta['base_revision_id'] === null) {
            return;
        }

        $current = $this->version();

        if ((int) $meta['base_revision_id'] !== $current) {
            throw StaleRevisionException::make(self::ENTRY_ID, (int) $meta['base_revision_id'], $current);
        }
    }

    /**
     * Every box on this form holds a single line of text, and every row it
     * writes holds a string. A null from an empty box becomes an empty string
     * rather than a null, so `config('site.email')` keeps getting the type it
     * has always had.
     */
    private function text(mixed $value): string
    {
        return is_scalar($value) ? trim((string) $value) : '';
    }
}
