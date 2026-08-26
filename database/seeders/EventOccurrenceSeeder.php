<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class EventOccurrenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call(EventSeeder::class);
    }
}
