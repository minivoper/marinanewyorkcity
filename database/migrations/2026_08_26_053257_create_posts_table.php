<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['news', 'guide'])->index();
            $table->string('slug')->unique();
            $table->string('title');
            $table->text('excerpt');
            $table->longText('body');
            $table->string('cover_path')->nullable();
            $table->timestamp('published_at')->index();
            $table->unsignedSmallInteger('read_minutes')->nullable();
            $table->string('meta_title');
            $table->text('meta_description');
            $table->text('geo_summary');
            $table->string('location_name')->nullable();
            $table->string('schema_type')->default('NewsArticle');
            $table->timestamps();

            $table->index(['type', 'published_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
