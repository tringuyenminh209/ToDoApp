<?php

namespace Database\Seeders;

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
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'NGUYEN MINH TRI',
            'email' => '2240788@ecc.ac.jp',
            'password' => '123456q12S'
        ]);

        // Seed Learning Path Templates
        $this->call([
            LearningPathTemplateSeeder::class,
        ]);
    }
}
