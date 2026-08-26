<?php

namespace App\Models;

use Database\Factories\PostFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'type',
    'slug',
    'title',
    'excerpt',
    'body',
    'cover_path',
    'published_at',
    'read_minutes',
    'meta_title',
    'meta_description',
    'geo_summary',
    'location_name',
    'schema_type',
])]
class Post extends Model
{
    public const TYPE_GUIDE = 'guide';

    public const TYPE_NEWS = 'news';

    /** @use HasFactory<PostFactory> */
    use HasFactory;

    protected $attributes = [
        'schema_type' => 'NewsArticle',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'read_minutes' => 'integer',
        ];
    }

    #[Scope]
    protected function published(Builder $query): Builder
    {
        return $query->where('published_at', '<=', now());
    }
}
