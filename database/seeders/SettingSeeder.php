<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            'site.name' => 'marina.newyorkcity',
            'site.email' => 'info@marinanewyorkcity.com',
            'site.emails' => ['info@marinanewyorkcity.com'],
            'site.socials' => [
                'instagram' => 'https://www.instagram.com/marina.newyorkcity/',
                'tiktok' => 'https://www.tiktok.com/@marina.newyorkcity',
                'threads' => 'https://www.threads.com/@marina.newyorkcity',
                'facebook' => 'https://www.facebook.com/marina.nycity',
                'kit' => 'https://kit.marinanewyorkcity.com',
                'links' => 'https://links.marinanewyorkcity.com',
            ],
            'site.monthly_views' => '5M+',
        ];

        foreach ($settings as $key => $value) {
            Setting::query()->updateOrCreate(
                ['key' => $key],
                ['value' => is_array($value) ? $value : ['value' => $value]],
            );
        }
    }
}
