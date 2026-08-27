<?php

namespace Database\Seeders;

use Eshlink\Cms\Models\CmsSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     *
     * These seeders `updateOrCreate` content and `EventSeeder` deletes and
     * recreates occurrences, so they are a first-run fixture, not something to
     * re-run. The deploy command runs `migrate --force --seed`; without this
     * guard every deploy would quietly overwrite whatever Marina had edited
     * since the last one, and an event's dates would be deleted and reinserted
     * under new ids while the site was serving them.
     *
     * The CMS bootstrap flag is what marks the site as past first run.
     */
    public function run(): void
    {
        if ($this->cmsIsInstalled()) {
            $this->command?->getOutput()->writeln(
                '<comment>CMS is installed; skipping the first-run content seeders so live content is not overwritten.</comment>',
            );

            return;
        }

        $this->call([
            PostSeeder::class,
            EventSeeder::class,
            PageSeeder::class,
            SettingSeeder::class,
        ]);
    }

    /**
     * False on a database whose `cms_settings` table does not exist yet, which
     * is the state a brand-new install is in the first time this runs.
     */
    private function cmsIsInstalled(): bool
    {
        return Schema::hasTable((new CmsSetting)->getTable()) && CmsSetting::isInstalled();
    }
}
