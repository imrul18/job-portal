<?php

namespace Database\Seeders;

use App\Models\CareerJob;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'iftekhar',
            'email' => 'iftekhar@gmail.com',
            'password' => bcrypt('iftekhar'),
        ]);

        User::factory()->create([
            'name' => 'imrul',
            'email' => 'imrul@gmail.com',
            'password' => bcrypt('imrul'),
        ]);

        CareerJob::factory()->create([
            'name' => 'Laravel Developer',
            'slug' => 'laravel-developer',
        ]);
    }
}
