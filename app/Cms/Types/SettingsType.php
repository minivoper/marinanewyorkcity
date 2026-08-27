<?php

namespace App\Cms\Types;

use App\Cms\Sources\SettingSource;
use App\Models\Setting;
use Eshlink\Cms\Contracts\ContentSource;
use Eshlink\Cms\Schema\Fields\Text;
use Eshlink\Cms\Schema\Fields\Textarea;
use Eshlink\Cms\Schema\Schema;

/**
 * Marina's `settings` key/value table, wrapped.
 *
 * It is a collection rather than the admin's single Settings screen because
 * that is what the table is: one row per key, each holding its own JSON value.
 * Pointing `cms.settings_type` at it would make the Settings screen edit
 * whichever row came back first, which is worse than the package's own "no
 * settings type declared" screen.
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
