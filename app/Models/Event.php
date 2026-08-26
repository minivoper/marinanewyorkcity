<?php

namespace App\Models;

use Database\Factories\EventFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'slug',
    'title',
    'excerpt',
    'body',
    'cover_path',
    'venue_name',
    'venue_address',
    'timezone',
    'meta_title',
    'meta_description',
    'geo_summary',
])]
class Event extends Model
{
    /** @use HasFactory<EventFactory> */
    use HasFactory;

    protected $attributes = [
        'timezone' => 'America/New_York',
    ];

    /**
     * @return HasMany<EventOccurrence, $this>
     */
    public function occurrences(): HasMany
    {
        return $this->hasMany(EventOccurrence::class);
    }
}
