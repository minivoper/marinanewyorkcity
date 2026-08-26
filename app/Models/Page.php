<?php

namespace App\Models;

use Database\Factories\PageFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['slug', 'title', 'body', 'meta_title', 'meta_description'])]
class Page extends Model
{
    /** @use HasFactory<PageFactory> */
    use HasFactory;
}
