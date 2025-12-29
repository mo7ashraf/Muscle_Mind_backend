<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Exercise;
use App\Models\Workout;
use Illuminate\Http\Request;

class WorkoutController extends Controller
{
    // GET /workouts
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Workout::query()->with(['exercises', 'trainer.user', 'trainee.user']);

        if ($user->role === 'trainer' && $user->trainerProfile) {
            $query->where('trainer_id', $user->trainerProfile->id);
        }

        if ($user->role === 'trainee' && $user->traineeProfile) {
            $query->where('trainee_id', $user->traineeProfile->id);
        }

        if ($request->filled('date')) {
            $query->whereDate('scheduled_date', $request->date('date'));
        }

        return response()->json($query->orderByDesc('id')->paginate(20));
    }

    // POST /workouts (trainer)
    public function store(Request $request)
    {
        $trainer = $request->user()->trainerProfile;
        if (!$trainer) return response()->json(['message' => 'Trainer profile not found'], 404);

        $validated = $request->validate([
            'trainee_id' => ['required', 'integer', 'exists:trainees,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'scheduled_date' => ['nullable', 'date'],
        ]);

        $workout = Workout::create([
            'trainee_id' => $validated['trainee_id'],
            'trainer_id' => $trainer->id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'scheduled_date' => $validated['scheduled_date'] ?? null,
            'completed' => false,
        ]);

        return response()->json(['workout' => $workout->load('exercises')], 201);
    }

    // GET /workouts/{id}
    public function show(Request $request, int $id)
    {
        $user = $request->user();
        $workout = Workout::with(['exercises', 'trainer.user', 'trainee.user'])->findOrFail($id);

        if ($user->role === 'trainer' && $user->trainerProfile && $workout->trainer_id !== $user->trainerProfile->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        if ($user->role === 'trainee' && $user->traineeProfile && $workout->trainee_id !== $user->traineeProfile->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return response()->json(['workout' => $workout]);
    }

    // PUT /workouts/{id}/complete (trainee)
    public function complete(Request $request, int $id)
    {
        $trainee = $request->user()->traineeProfile;
        if (!$trainee) return response()->json(['message' => 'Trainee profile not found'], 404);

        $workout = Workout::where('trainee_id', $trainee->id)->findOrFail($id);
        $workout->completed = true;
        $workout->save();

        return response()->json(['workout' => $workout->fresh()]);
    }

    // POST /workouts/{id}/exercises (trainer)
    public function addExercise(Request $request, int $id)
    {
        $trainer = $request->user()->trainerProfile;
        if (!$trainer) return response()->json(['message' => 'Trainer profile not found'], 404);

        $workout = Workout::where('trainer_id', $trainer->id)->findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sets' => ['nullable', 'integer', 'min:0', 'max:999'],
            'reps' => ['nullable', 'integer', 'min:0', 'max:999'],
            'rest_time' => ['nullable', 'integer', 'min:0', 'max:99999'],
            'notes' => ['nullable', 'string'],
            'video_url' => ['nullable', 'url', 'max:1000'],
        ]);

        $exercise = Exercise::create(array_merge($validated, [
            'workout_id' => $workout->id,
        ]));

        return response()->json(['exercise' => $exercise], 201);
    }
}
