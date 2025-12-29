<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DietPlan;
use App\Models\Meal;
use Illuminate\Http\Request;

class DietPlanController extends Controller
{
    // GET /diet-plans
    public function index(Request $request)
    {
        $user = $request->user();

        $query = DietPlan::query()->with(['meals', 'trainer.user', 'trainee.user']);

        if ($user->role === 'trainer' && $user->trainerProfile) {
            $query->where('trainer_id', $user->trainerProfile->id);
        }

        if ($user->role === 'trainee' && $user->traineeProfile) {
            $query->where('trainee_id', $user->traineeProfile->id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        return response()->json($query->orderByDesc('id')->paginate(20));
    }

    // POST /diet-plans (trainer)
    public function store(Request $request)
    {
        $trainer = $request->user()->trainerProfile;
        if (!$trainer) return response()->json(['message' => 'Trainer profile not found'], 404);

        $validated = $request->validate([
            'trainee_id' => ['required', 'integer', 'exists:trainees,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'calories_target' => ['nullable', 'integer', 'min:0', 'max:99999'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'status' => ['nullable', 'in:active,completed'],
        ]);

        $plan = DietPlan::create([
            'trainee_id' => $validated['trainee_id'],
            'trainer_id' => $trainer->id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'calories_target' => $validated['calories_target'] ?? null,
            'start_date' => $validated['start_date'] ?? null,
            'end_date' => $validated['end_date'] ?? null,
            'status' => $validated['status'] ?? 'active',
        ]);

        return response()->json(['diet_plan' => $plan->load('meals')], 201);
    }

    // GET /diet-plans/{id}
    public function show(Request $request, int $id)
    {
        $user = $request->user();
        $plan = DietPlan::with(['meals', 'trainer.user', 'trainee.user'])->findOrFail($id);

        if ($user->role === 'trainer' && $user->trainerProfile && $plan->trainer_id !== $user->trainerProfile->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        if ($user->role === 'trainee' && $user->traineeProfile && $plan->trainee_id !== $user->traineeProfile->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return response()->json(['diet_plan' => $plan]);
    }

    // PUT /diet-plans/{id} (trainer)
    public function update(Request $request, int $id)
    {
        $trainer = $request->user()->trainerProfile;
        if (!$trainer) return response()->json(['message' => 'Trainer profile not found'], 404);

        $plan = DietPlan::where('trainer_id', $trainer->id)->findOrFail($id);

        $validated = $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'calories_target' => ['sometimes', 'nullable', 'integer', 'min:0', 'max:99999'],
            'start_date' => ['sometimes', 'nullable', 'date'],
            'end_date' => ['sometimes', 'nullable', 'date', 'after_or_equal:start_date'],
            'status' => ['sometimes', 'in:active,completed'],
        ]);

        $plan->fill($validated)->save();

        return response()->json(['diet_plan' => $plan->fresh()->load('meals')]);
    }

    // DELETE /diet-plans/{id} (trainer)
    public function destroy(Request $request, int $id)
    {
        $trainer = $request->user()->trainerProfile;
        if (!$trainer) return response()->json(['message' => 'Trainer profile not found'], 404);

        $plan = DietPlan::where('trainer_id', $trainer->id)->findOrFail($id);
        $plan->delete();

        return response()->json(['message' => 'Deleted']);
    }

    // POST /diet-plans/{id}/meals (trainer)
    public function addMeal(Request $request, int $id)
    {
        $trainer = $request->user()->trainerProfile;
        if (!$trainer) return response()->json(['message' => 'Trainer profile not found'], 404);

        $plan = DietPlan::where('trainer_id', $trainer->id)->findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'time' => ['nullable', 'string', 'max:20'],
            'calories' => ['nullable', 'integer', 'min:0', 'max:99999'],
            'proteins' => ['nullable', 'numeric', 'min:0', 'max:999'],
            'carbs' => ['nullable', 'numeric', 'min:0', 'max:999'],
            'fats' => ['nullable', 'numeric', 'min:0', 'max:999'],
            'description' => ['nullable', 'string'],
        ]);

        $meal = Meal::create(array_merge($validated, [
            'diet_plan_id' => $plan->id,
        ]));

        return response()->json(['meal' => $meal], 201);
    }
}
