<?php

namespace App\Cms\Types;

use App\Models\MediaAsset;
use Eshlink\Cms\Contracts\ContentSource;
use Eshlink\Cms\Schema\Fields\Number;
use Eshlink\Cms\Schema\Fields\Text;
use Eshlink\Cms\Schema\Fields\Url;
use Eshlink\Cms\Schema\Schema;
use Eshlink\Cms\Sources\ModelSource;
use Eshlink\Cms\Support\SiteMap;

/**
 * Marina's own `media_assets` catalogue, wrapped so the alt text and credits
 * she records against a file are editable.
 *
 * This is not the CMS media library. The package's `cms_media` table is
 * content-addressed object storage with an upload pipeline behind it; this
 * table is a plain index of the 159 MB of files already sitting under
 * `public/media`, and the two are kept separate rather than merged because
 * nothing on the public site references a `cms_media` id yet.
 *
 * `alt` is required at the schema level. That is the accessibility rule the
 * plan asks for, applied at the only point where it can actually be enforced.
 */
class MediaAssetType extends BaseType
{
    public function key(): string
    {
        return 'media_asset';
    }

    public function label(): string
    {
        return 'Photo description';
    }

    public function pluralLabel(): string
    {
        return 'Photo descriptions';
    }

    public function blurb(): ?string
    {
        return 'Alt text and credits for pictures already on your site.';
    }

    /**
     * A catalogue of rows about pictures, not the pictures themselves — she
     * comes here to fix a caption, never to write something new. That is
     * Setup, whatever `SiteMap` would infer from it not being a singleton.
     */
    public function group(): ?string
    {
        return SiteMap::GROUP_SETUP;
    }

    public function schema(): Schema
    {
        return Schema::make([
            Text::make('path')->required()->max(255)
                ->help('Path under public/, e.g. media/posts/example.jpg.'),
            Text::make('alt')->required()->max(255)
                ->help('What the picture shows, for anyone who cannot see it.'),
            Text::make('credit')->max(255),
            Url::make('source_url')->schemes(['https', 'http'])->max(255),
            Number::make('width')->integer()->min(1),
            Number::make('height')->integer()->min(1),
        ]);
    }

    public function source(): ContentSource
    {
        return new ModelSource(
            modelClass: MediaAsset::class,
            map: [
                'path' => 'path',
                'alt' => 'alt',
                'credit' => 'credit',
                'source_url' => 'source_url',
                'width' => 'width',
                'height' => 'height',
            ],
            titleColumn: 'alt',
        );
    }

    /**
     * @return array{read: string, write: string, publish: string, delete: string}
     */
    public function abilities(): array
    {
        return [
            'read' => 'media.read',
            'write' => 'media.write',
            'publish' => 'media.write',
            'delete' => 'media.write',
        ];
    }
}
