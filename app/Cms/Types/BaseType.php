<?php

namespace App\Cms\Types;

use Eshlink\Cms\Contracts\ContentRule;
use Eshlink\Cms\Contracts\ContentType;

/**
 * Shared answers for the parts of the {@see ContentType} contract that are the
 * same for almost every type this site declares.
 *
 * Only `key()`, `label()`, `schema()` and `source()` stay abstract, because
 * those are the four things that actually differ between a blog post and the
 * home page. Everything else has one obviously correct answer here and is
 * overridden where it is not.
 */
abstract class BaseType implements ContentType
{
    public function pluralLabel(): string
    {
        return $this->label();
    }

    public function isSingleton(): bool
    {
        return false;
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
