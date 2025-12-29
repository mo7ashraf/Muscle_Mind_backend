<?php

use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ChallengeController;
use App\Http\Controllers\Api\DietPlanController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ProgressController;
use App\Http\Controllers\Api\TrainerTraineeController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\WorkoutController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

// Public Articles
Route::get('/articles', [ArticleController::class, 'index']);
Route::get('/articles/{id}', [ArticleController::class, 'show']);

/*
|--------------------------------------------------------------------------
| Protected Routes (Sanctum)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);

    // User
    Route::get('/user', [UserController::class, 'me']);
    Route::put('/user/profile', [UserController::class, 'updateProfile']);
    Route::post('/user/avatar', [UserController::class, 'uploadAvatar']);

    // Shared access (trainer or trainee) — filtered internally
    Route::get('/diet-plans', [DietPlanController::class, 'index']);
    Route::get('/diet-plans/{id}', [DietPlanController::class, 'show']);

    Route::get('/workouts', [WorkoutController::class, 'index']);
    Route::get('/workouts/{id}', [WorkoutController::class, 'show']);

    Route::get('/challenges', [ChallengeController::class, 'index']);

    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::put('/notifications/{id}/read', [NotificationController::class, 'markRead']);

    // Trainer-only
    Route::middleware('role:trainer')->group(function () {
        Route::get('/trainer/trainees', [TrainerTraineeController::class, 'index']);
        Route::get('/trainer/trainees/{id}', [TrainerTraineeController::class, 'show']);
        Route::post('/trainer/trainees', [TrainerTraineeController::class, 'store']);
        Route::put('/trainer/trainees/{id}', [TrainerTraineeController::class, 'update']);

        Route::post('/diet-plans', [DietPlanController::class, 'store']);
        Route::put('/diet-plans/{id}', [DietPlanController::class, 'update']);
        Route::delete('/diet-plans/{id}', [DietPlanController::class, 'destroy']);
        Route::post('/diet-plans/{id}/meals', [DietPlanController::class, 'addMeal']);

        Route::post('/workouts', [WorkoutController::class, 'store']);
        Route::post('/workouts/{id}/exercises', [WorkoutController::class, 'addExercise']);

        Route::post('/articles', [ArticleController::class, 'store']);
    });

    // Trainee-only
    Route::middleware('role:trainee')->group(function () {
        Route::get('/trainee/progress', [ProgressController::class, 'progress']);
        Route::post('/trainee/progress/photos', [ProgressController::class, 'uploadProgressPhotos']);
        Route::post('/trainee/measurements', [ProgressController::class, 'addMeasurement']);
        Route::get('/trainee/measurements/history', [ProgressController::class, 'measurementHistory']);

        Route::put('/workouts/{id}/complete', [WorkoutController::class, 'complete']);

        Route::post('/challenges/join/{id}', [ChallengeController::class, 'join']);
        Route::post('/challenges/checkin/{id}', [ChallengeController::class, 'checkin']);
        Route::get('/trainee/challenges', [ChallengeController::class, 'myChallenges']);
    });

});
