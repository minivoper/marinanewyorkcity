<?php

namespace App\Cms\Types;

use Eshlink\Cms\Contracts\ContentRule;
use Eshlink\Cms\Contracts\ContentType;
use Eshlink\Cms\Contracts\Presentable;

/**
 * Shared answers for the parts of the {@see ContentType} contract that are the
 * same for almost every type this site declares.
 *
 * Only `key()`, `label()`, `schema()` and `source()` stay abstract, because
 * those are the four things that actually differ between a blog post and the
 * home page. Everything else has one obviously correct answer here and is
 * overridden where it is not.
 *
 * {@see Presentable} is implemented here rather than type by type so that every
 * type this site declares is guaranteed to answer the admin's three questions,
 * even if the answer is "nothing in particular". Marina never sees a card whose
 * blurb is a stray `null` on one type and a sentence on its neighbour.
 */
abstract class BaseType implements ContentType, Presentable
{
    public function pluralLabel(): string
    {
        return $this->label();
    }

    public function isSingleton(): bool
    {
        return false;
    }

    /**
     * Nothing rather than something generic. A card that says "A content type"
     * under its name has spent a line of the screen telling her what she
     * already knew; every type that has something worth saying says it.
     */
    public function blurb(): ?string
    {
        return null;
    }

    /**
     * Null defers to `SiteMap`, which sorts a singleton into Pages and
     * everything else into Collections. That is right for most of this site
     * and wrong only where a type is machinery — settings, the photo
     * catalogue — which says so itself.
     */
    public function group(): ?string
    {
        return null;
    }

    /**
     * Always null on this site. `SiteMap` puts the first letter of the label on
     * the plate, and a letter Marina can read off the sidebar beats a symbol
     * somebody chose for her.
     */
    public function glyph(): ?string
    {
        return null;
    }

    public function hasSlug(): bool
    {
        return false;
    }

    public function isOrderable(): bool
    {
        return false;
    }

    /**
     * Marina owns her own site and publishes her own words. The approval gate
     * exists for davidkober's compliance lock, not here.
     */
    public function requiresApproval(): bool
    {
        return false;
    }

    /**
     * @return array<int, ContentRule>
     */
    public function rules(): array
    {
        return [];
    }

    /**
     * Empty by default, which is the correct answer for every type that wraps
     * one of Marina's existing models: those rows are already in her database
     * and `cms:install --seed-defaults` must not write over them. The page
     * singletons override this with the literals their Blade template used to
     * hardcode.
     *
     * @return array<string, mixed>
     */
    public function defaults(): array
    {
        return [];
    }

    /**
     * @return array{read: string, write: string, publish: string, delete: string}
     */
    public function abilities(): array
    {
        return [
            'read' => 'content.read',
            'write' => 'content.write',
            'publish' => 'content.publish',
            'delete' => 'content.delete',
        ];
    }
}
