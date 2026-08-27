<?php

namespace App\Cms\Types;

use App\Cms\Sources\SettingSource;
use App\Models\Setting;
use Eshlink\Cms\Contracts\ContentSource;
use Eshlink\Cms\Schema\Fields\Text;
use Eshlink\Cms\Schema\Fields\Textarea;
use Eshlink\Cms\Schema\Schema;

/**
 * Marina's `settings` key/value table, wrapped one row at a time.
 *
 * NO LONGER REGISTERED. `config/cms.php` lists {@see SiteSettingsType} instead,
 * which presents the same rows as one form of labelled boxes. This class showed
 * them as five rows whose Value field was hand-written JSON, and a second thing
 * called Settings in the sidebar besides — the admin now has exactly one.
 *
 * It is kept on disk, unlisted, because `tests/Feature/CmsModelSourceTest.php`
 * still exercises it: `settings.value` is the one column on this site behind an
 * `array` cast, and {@see SettingSource} is the only worked
 * example of adapting a cast column to a scalar field schema. Delete both, and
 * that test, together or not at all.
 */
class SettingsType extends BaseType
{
    public function key(): string
    {
        return 'setting';
    }

    public function label(): string
    {
        return 'Setting';
    }

    public function pluralLabel(): string
    {
        return 'Settings';
    }

    public function hasSlug(): bool
    {
        return true;
    }

    public function schema(): Schema
    {
        return Schema::make([
            Text::make('key')->required()->max(255)
                ->help('The name this value is looked up by, e.g. site.email.'),
            Textarea::make('value')->required()->max(20000)->rows(8)->rules(['json'])
                ->help('JSON. A plain setting is stored as {"value": "..."}; a list or a map is stored as itself.'),
        ]);
    }

    public function source(): ContentSource
    {
        return new SettingSource(
            modelClass: Setting::class,
            map: [
                'key' => 'key',
                'value' => 'value',
            ],
            slugColumn: 'key',
            titleColumn: 'key',
        );
    }

    /**
     * @return array{read: string, write: string, publish: string, delete: string}
     */
    public function abilities(): array
    {
        return [
            'read' => 'content.read',
            'write' => 'settings.write',
            'publish' => 'settings.write',
            'delete' => 'settings.write',
        ];
    }
}
