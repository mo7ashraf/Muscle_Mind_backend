<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Trainer;
use App\Models\Trainee;
use App\Models\Article;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $trainerUser = User::factory()->create([
            'name' => 'Demo Trainer',
            'email' => 'trainer@example.com',
            'role' => 'trainer',
        ]);

        $traineeUser = User::factory()->create([
            'name' => 'Demo Trainee',
            'email' => 'trainee@example.com',
            'role' => 'trainee',
        ]);

        $trainer = Trainer::create([
            'user_id' => $trainerUser->id,
            'specialization' => 'Strength Training',
            'experience_years' => 5,
            'certification' => 'CPT',
            'bio' => 'Demo trainer for local testing.',
            'rating' => 4.5,
        ]);

        Trainee::create([
            'user_id' => $traineeUser->id,
            'trainer_id' => $trainer->id,
            'current_weight' => 80,
            'target_weight' => 75,
            'height' => 175,
            'age' => 28,
            'gender' => 'male',
            'goal' => 'weight_loss',
            'starting_date' => now()->toDateString(),
        ]);

        Article::create([
            'title_en' => 'Welcome to Muscle Mind',
            'title_ar' => null,
            'content_en' => 'This is a seeded article to help you test the API.',
            'content_ar' => null,
            'image' => null,
            'category' => 'general',
            'author_id' => $trainerUser->id,
            'published_at' => now(),
        ]);
    }
}
