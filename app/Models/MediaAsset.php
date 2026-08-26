<?php

namespace App\Models;

use Database\Factories\MediaAssetFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['path', 'alt', 'credit', 'source_url', 'width', 'height'])]
class MediaAsset extends Model
{
    /** @use HasFactory<MediaAssetFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'width' => 'integer',
            'height' => 'integer',
        ];
    }
}
