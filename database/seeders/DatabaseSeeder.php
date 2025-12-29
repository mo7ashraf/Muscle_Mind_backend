<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Challenge;
use App\Models\DietPlan;
use App\Models\Exercise;
use App\Models\Meal;
use App\Models\Measurement;
use App\Models\Notification;
use App\Models\ProgressPhoto;
use App\Models\Trainee;
use App\Models\TraineeChallenge;
use App\Models\Trainer;
use App\Models\User;
use App\Models\Workout;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Core demo users (idempotent)
        $trainerUser = User::firstWhere('email', 'trainer@example.com') ?? User::create([
            'name' => 'Demo Trainer',
            'email' => 'trainer@example.com',
            'password' => Hash::make('password'),
            'role' => 'trainer',
        ]);

        $traineeUser = User::firstWhere('email', 'trainee@example.com') ?? User::create([
            'name' => 'Demo Trainee',
            'email' => 'trainee@example.com',
            'password' => Hash::make('password'),
            'role' => 'trainee',
        ]);

        $trainer = Trainer::firstOrCreate(
            ['user_id' => $trainerUser->id],
            [
                'specialization' => 'Strength Training',
                'experience_years' => 5,
                'certification' => 'CPT',
                'bio' => 'Demo trainer for local testing.',
                'rating' => 4.5,
            ]
        );

        $trainee = Trainee::firstOrCreate(
            ['user_id' => $traineeUser->id],
            [
                'trainer_id' => $trainer->id,
                'current_weight' => 80,
                'target_weight' => 75,
                'height' => 175,
                'age' => 28,
                'gender' => 'male',
                'goal' => 'weight_loss',
                'starting_date' => now()->toDateString(),
            ]
        );

        Article::firstOrCreate(
            [
                'title_en' => 'Welcome to Muscle Mind',
                'author_id' => $trainerUser->id,
            ],
            [
                'title_ar' => null,
                'content_en' => 'This is a seeded article to help you test the API.',
                'content_ar' => null,
                'image' => null,
                'category' => 'general',
                'published_at' => now(),
            ]
        );

        // Domain tables: only seed if currently empty

        $challenge = Challenge::first();
        if (! $challenge) {
            $challenge = Challenge::create([
                'title' => '30-Day Fat Loss Challenge',
                'description' => 'Daily workouts and nutrition targets to kickstart fat loss.',
                'duration_days' => 30,
                'image' => null,
                'difficulty_level' => 'intermediate',
                'is_active' => true,
            ]);
        }

        if (! TraineeChallenge::query()->exists()) {
            TraineeChallenge::create([
                'trainee_id' => $trainee->id,
                'challenge_id' => $challenge->id,
                'start_date' => now()->subDays(3)->toDateString(),
                'status' => 'ongoing',
                'completed_days' => 3,
                'last_check_in' => now()->subDay()->toDateString(),
            ]);
        }

        $dietPlan = DietPlan::first();
        if (! $dietPlan) {
            $dietPlan = DietPlan::create([
                'trainee_id' => $trainee->id,
                'trainer_id' => $trainer->id,
                'title' => 'Cutting Plan - Week 1',
                'description' => 'High protein, moderate carbs, controlled fats.',
                'calories_target' => 2200,
                'start_date' => now()->toDateString(),
                'end_date' => now()->addWeek()->toDateString(),
                'status' => 'active',
            ]);
        }

        if (! Meal::query()->exists()) {
            Meal::insert([
                [
                    'diet_plan_id' => $dietPlan->id,
                    'name' => 'Breakfast - Oats & Eggs',
                    'time' => '08:00',
                    'calories' => 500,
                    'proteins' => 30,
                    'carbs' => 50,
                    'fats' => 15,
                    'description' => 'Oatmeal with 3 egg whites and 1 whole egg.',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'diet_plan_id' => $dietPlan->id,
                    'name' => 'Lunch - Grilled Chicken & Rice',
                    'time' => '13:00',
                    'calories' => 650,
                    'proteins' => 45,
                    'carbs' => 70,
                    'fats' => 15,
                    'description' => 'Grilled chicken breast with basmati rice and veggies.',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }

        $workout = Workout::first();
        if (! $workout) {
            $workout = Workout::create([
                'trainee_id' => $trainee->id,
                'trainer_id' => $trainer->id,
                'title' => 'Full Body Workout',
                'description' => 'Compound movements focusing on strength and hypertrophy.',
                'scheduled_date' => now()->addDay()->toDateString(),
                'completed' => false,
            ]);
        }

        if (! Exercise::query()->exists()) {
            Exercise::insert([
                [
                    'workout_id' => $workout->id,
                    'name' => 'Squats',
                    'sets' => 4,
                    'reps' => 8,
                    'rest_time' => 90,
                    'notes' => 'Focus on depth and control.',
                    'video_url' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'workout_id' => $workout->id,
                    'name' => 'Bench Press',
                    'sets' => 4,
                    'reps' => 8,
                    'rest_time' => 90,
                    'notes' => 'Keep shoulders retracted, full ROM.',
                    'video_url' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }

        if (! Measurement::query()->exists()) {
            Measurement::insert([
                [
                    'trainee_id' => $trainee->id,
                    'weight' => 80,
                    'chest' => 100,
                    'waist' => 90,
                    'hips' => 98,
                    'arms' => 35,
                    'thighs' => 55,
                    'measured_at' => now()->subDays(7),
                    'created_at' => now()->subDays(7),
                    'updated_at' => now()->subDays(7),
                ],
                [
                    'trainee_id' => $trainee->id,
                    'weight' => 78.5,
                    'chest' => 99,
                    'waist' => 88,
                    'hips' => 97,
                    'arms' => 35,
                    'thighs' => 54.5,
                    'measured_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }

        if (! ProgressPhoto::query()->exists()) {
            ProgressPhoto::create([
                'trainee_id' => $trainee->id,
                'front_image' => null,
                'back_image' => null,
                'side_image' => null,
                'weight' => 78.5,
                'notes' => 'End of week 1 progress photo.',
                'taken_at' => now(),
            ]);
        }

        if (! Notification::query()->exists()) {
            Notification::insert([
                [
                    'user_id' => $traineeUser->id,
                    'title' => 'Welcome to Muscle Mind',
                    'message' => 'Your demo account is ready. Start tracking your progress today!',
                    'type' => 'system',
                    'read_at' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'user_id' => $trainerUser->id,
                    'title' => 'New Trainee Assigned',
                    'message' => 'Demo Trainee has been assigned to you. Set up their plan.',
                    'type' => 'system',
                    'read_at' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }
    }
}
