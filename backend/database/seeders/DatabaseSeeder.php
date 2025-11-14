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

        // Seed Learning Path Templates and CheatCode Data
        $this->call([
            LearningPathTemplateSeeder::class,
            JavaBasicCourseSeeder::class,
            JavaDesignCourseSeeder::class,
            PhpBasicCourseSeeder::class,
            LaravelCourseSeeder::class,
            CheatCodePhpSeeder::class,
            CheatCodePhpExerciseSeeder::class,
            CheatCodeJavaSeeder::class,
            CheatCodeJavaExerciseSeeder::class,
            CheatCodeGoSeeder::class,
            CheatCodeGoExerciseSeeder::class,
            CheatCodeJavaScriptSeeder::class,
            CheatCodeJavaScriptExerciseSeeder::class,
            CheatCodePythonSeeder::class,
            CheatCodePythonExerciseSeeder::class,
            CheatCodeCppSeeder::class,
            CheatCodeCppExerciseSeeder::class,
            CheatCodeKotlinSeeder::class,
            CheatCodeKotlinExerciseSeeder::class,
            CheatCodeCss3Seeder::class,
            CheatCodeHtmlSeeder::class,
            CheatCodeBashSeeder::class,
            CheatCodeBashExerciseSeeder::class,
            CheatCodeLaravelSeeder::class,
            CheatCodeDockerSeeder::class,
            CheatCodeYamlSeeder::class,
            CheatCodeMysqlSeeder::class,
        ]);
    }
}
