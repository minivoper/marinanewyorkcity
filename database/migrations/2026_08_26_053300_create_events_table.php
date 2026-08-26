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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->text('excerpt');
            $table->longText('body');
            $table->string('cover_path')->nullable();
            $table->string('venue_name');
            $table->string('venue_address');
            $table->string('timezone')->default('America/New_York');
            $table->string('meta_title');
            $table->text('meta_description');
            $table->text('geo_summary');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
