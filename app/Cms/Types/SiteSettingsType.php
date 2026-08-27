<?php

namespace App\Cms\Types;

use App\Cms\Sources\SiteSettingsSource;
use Eshlink\Cms\Contracts\ContentSource;
use Eshlink\Cms\Schema\Fields\Text;
use Eshlink\Cms\Schema\Fields\Url;
use Eshlink\Cms\Schema\Schema;
use Eshlink\Cms\Support\SiteMap;

/**
 * The one Settings screen.
 *
 * There used to be two, and both were wrong. `/settings` rendered a placeholder
 * telling the site's owner to edit `config/cms.php`, which she cannot read and
 * would not be allowed to deploy. `/content/setting` listed five rows whose
 * Value field was hand-written JSON — a format, not a question. Between them
 * they managed to make "what is my email address" a task requiring a developer.
 *
 * This is that question asked ten times, once per box. The stored shape is
 * unchanged: {@see SiteSettingsSource} fans these fields back out to the same
 * `settings` rows in the same JSON they have always held, so nothing that reads
 * them can tell the difference.
 */
class SiteSettingsType extends BaseType
{
    public function key(): string
    {
        return 'site_settings';
    }

    public function label(): string
    {
        return 'Settings';
    }

    public function pluralLabel(): string
    {
        return 'Settings';
    }

    /**
     * One site, one set of settings. This is what points `/settings` at a real
     * form instead of the package's "no settings type declared" placeholder.
     */
    public function isSingleton(): bool
    {
        return true;
    }

    public function blurb(): ?string
    {
        return 'Your name, your email address and your links.';
    }

    public function group(): ?string
    {
        return SiteMap::GROUP_SETUP;
    }

    /**
     * Flat, and every box a labelled box.
     *
     * The six social addresses are stored together in one `site.socials` map
     * and are presented as six separate fields, because "paste your Instagram
     * link here" is answerable and "edit this map" is not. The help text says
     * what to paste rather than what the field is: she can already see it is
     * called Instagram.
     */
    public function schema(): Schema
    {
        $link = 'Paste the full web address, starting with https://';

        return Schema::make([
            Text::make('name')->withLabel('Site name')->required()->max(120)
                ->help('The name shown in the corner of every page.'),

            Text::make('email')->withLabel('Contact email')->required()->max(255)->rules(['email'])
                ->help('Where the contact form sends messages.'),

            Url::make('instagram')->withLabel('Instagram')->help($link),
            Url::make('tiktok')->withLabel('TikTok')->help($link),
            Url::make('threads')->withLabel('Threads')->help($link),
            Url::make('facebook')->withLabel('Facebook')->help($link),

            Url::make('kit')->withLabel('Kit page')->help($link),
            Url::make('links')->withLabel('Links page')->help($link),

            Text::make('monthly_views')->withLabel('Monthly views')->max(60)
                ->help('Shown on your press page, e.g. 5M+'),
        ]);
    }

    public function source(): ContentSource
    {
        return new SiteSettingsSource;
    }

    /**
     * Writing these needs `settings.write`, which owners hold and editors do
     * not. A wrong headline is a bad afternoon; a wrong contact address is mail
     * nobody receives and nobody notices.
     *
     * The same ability guards reading, because these rows are live the moment
     * they are saved: there is no draft copy to look at, so an editor let in to
     * read would be looking at a form whose only button is refused.
     *
     * @return array{read: string, write: string, publish: string, delete: string}
     */
    public function abilities(): array
    {
        return [
            'read' => 'settings.write',
            'write' => 'settings.write',
            'publish' => 'settings.write',
            'delete' => 'settings.write',
        ];
    }
}
