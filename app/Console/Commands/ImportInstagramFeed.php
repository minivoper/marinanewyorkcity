<?php

namespace App\Console\Commands;

use App\Cms\Types\InstagramFeedType;
use Eshlink\Cms\Services\EntryService;
use Eshlink\Cms\Support\TypeRegistry;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * Imports the Instagram strip from the Wix harvest into the CMS.
 *
 * The home page used to read `docs/wix-ref/instagram.json` from disk on every
 * request and derive each tile's alt text by truncating the first line of the
 * caption. That import has already been run once — its result is
 * {@see InstagramFeedType::defaults()} — so this command exists for the second
 * time, when the strip is re-harvested and 30 new tiles need to land without
 * anyone hand-editing a PHP array.
 *
 * It is idempotent: the strip is a singleton, so a second run replaces the
 * items rather than appending a second copy.
 */
class ImportInstagramFeed extends Command
{
    protected $signature = 'cms:import-instagram
        {--path= : The harvest to read. Defaults to docs/wix-ref/instagram.json.}
        {--publish : Publish the strip as well as saving it, which is what makes it visible.}';

    protected $description = 'Import the Instagram strip on the home page from a Wix harvest file.';

    public function handle(TypeRegistry $types, EntryService $entries): int
    {
        $path = $this->option('path') ?: base_path('docs/wix-ref/instagram.json');

        if (! File::exists($path)) {
            $this->components->error("No harvest at {$path}.");

            return self::FAILURE;
        }

        $type = $types->get((new InstagramFeedType)->key());
        $items = $this->items($path);

        if ($items === []) {
            $this->components->error('The harvest has no downloaded items, so importing it would empty the strip.');

            return self::FAILURE;
        }

        $meta = ['actor_name' => 'cms:import-instagram', 'actor_role' => 'superadmin'];

        $entry = $entries->importBySlug($type, null, [
            'profile_url' => $type->defaults()['profile_url'],
            'items' => $items,
        ], $meta);

        if ($this->option('publish')) {
            $entries->publish($type, $entry['id'], $meta);
        }

        $this->components->info(sprintf(
            '%d tiles imported%s.',
            count($items),
            $this->option('publish') ? ' and published' : ' as a draft; run again with --publish to make them visible',
        ));

        return self::SUCCESS;
    }

    /**
     * The harvest's downloaded tiles, in file order.
     *
     * A tile that was never downloaded has no local file behind it, so
     * importing it would put a broken image on the home page.
     *
     * @return array<int, array{index: int, path: string, alt: string}>
     */
    private function items(string $path): array
    {
        $items = File::json($path)['items'] ?? [];

        return array_values(array_map(
            static fn (array $item): array => [
                'index' => (int) $item['index'],
                'path' => str_replace('public/', '', (string) $item['local_path']),
                // The alt text the home page used to derive on every render:
                // the caption's first line, cut to something a screen reader
                // can get through.
                'alt' => trim(Str::limit(strtok((string) $item['caption'], "\n"), 120)),
            ],
            array_filter(
                $items,
                static fn (array $item): bool => ($item['downloaded'] ?? false) && isset($item['local_path']),
            ),
        ));
    }
}
