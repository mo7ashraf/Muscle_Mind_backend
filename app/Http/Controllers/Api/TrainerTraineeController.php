<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Trainee;
use App\Models\Trainer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password as PasswordRule;

class TrainerTraineeController extends Controller
{
    // GET /trainer/trainees
    public function index(Request $request)
    {
        $trainer = $request->user()->trainerProfile;
        if (!$trainer) return response()->json(['message' => 'Trainer profile not found'], 404);

        $trainees = Trainee::with('user')
            ->where('trainer_id', $trainer->id)
            ->orderByDesc('id')
            ->paginate(20);

        return response()->json($trainees);
    }

    // GET /trainer/trainees/{id}
    public function show(Request $request, int $id)
    {
        $trainer = $request->user()->trainerProfile;
        if (!$trainer) return response()->json(['message' => 'Trainer profile not found'], 404);

        $trainee = Trainee::with(['user', 'measurements', 'progressPhotos'])
            ->where('trainer_id', $trainer->id)
            ->findOrFail($id);

        return response()->json(['trainee' => $trainee]);
    }

    // POST /trainer/trainees (creates a new trainee + user)
    public function store(Request $request)
    {
        $trainer = $request->user()->trainerProfile;
        if (!$trainer) return response()->json(['message' => 'Trainer profile not found'], 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', PasswordRule::min(8)],
            'phone' => ['nullable', 'string', 'max:50'],

            'current_weight' => ['nullable', 'numeric', 'min:0', 'max:999'],
            'target_weight' => ['nullable', 'numeric', 'min:0', 'max:999'],
            'height' => ['nullable', 'numeric', 'min:0', 'max:999'],
            'age' => ['nullable', 'integer', 'min:0', 'max:120'],
            'gender' => ['nullable', 'in:male,female,other'],
            'goal' => ['nullable', 'in:weight_loss,muscle_gain,maintenance'],
            'starting_date' => ['nullable', 'date'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
            'role' => 'trainee',
        ]);

        $trainee = Trainee::create([
            'user_id' => $user->id,
            'trainer_id' => $trainer->id,
            'current_weight' => $validated['current_weight'] ?? null,
            'target_weight' => $validated['target_weight'] ?? null,
            'height' => $validated['height'] ?? null,
            'age' => $validated['age'] ?? null,
            'gender' => $validated['gender'] ?? null,
            'goal' => $validated['goal'] ?? 'maintenance',
            'starting_date' => $validated['starting_date'] ?? null,
        ]);

        return response()->json([
            'trainee' => $trainee->load('user'),
        ], 201);
    }

    // PUT /trainer/trainees/{id}
    public function update(Request $request, int $id)
    {
        $trainer = $request->user()->trainerProfile;
        if (!$trainer) return response()->json(['message' => 'Trainer profile not found'], 404);

        $trainee = Trainee::with('user')
            ->where('trainer_id', $trainer->id)
            ->findOrFail($id);

        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:50'],

            'current_weight' => ['sometimes', 'nullable', 'numeric', 'min:0', 'max:999'],
            'target_weight' => ['sometimes', 'nullable', 'numeric', 'min:0', 'max:999'],
            'height' => ['sometimes', 'nullable', 'numeric', 'min:0', 'max:999'],
            'age' => ['sometimes', 'nullable', 'integer', 'min:0', 'max:120'],
            'gender' => ['sometimes', 'nullable', 'in:male,female,other'],
            'goal' => ['sometimes', 'nullable', 'in:weight_loss,muscle_gain,maintenance'],
            'starting_date' => ['sometimes', 'nullable', 'date'],
        ]);

        if (isset($validated['name']) || array_key_exists('phone', $validated)) {
            $trainee->user->fill([
                'name' => $validated['name'] ?? $trainee->user->name,
                'phone' => array_key_exists('phone', $validated) ? $validated['phone'] : $trainee->user->phone,
            ])->save();
        }

        $trainee->fill([
            'current_weight' => $validated['current_weight'] ?? $trainee->current_weight,
            'target_weight' => $validated['target_weight'] ?? $trainee->target_weight,
            'height' => $validated['height'] ?? $trainee->height,
            'age' => $validated['age'] ?? $trainee->age,
            'gender' => $validated['gender'] ?? $trainee->gender,
            'goal' => $validated['goal'] ?? $trainee->goal,
            'starting_date' => $validated['starting_date'] ?? $trainee->starting_date,
        ])->save();

        return response()->json([
            'trainee' => $trainee->fresh()->load('user'),
        ]);
    }
}
