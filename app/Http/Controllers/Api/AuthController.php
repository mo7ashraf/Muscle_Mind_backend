<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Trainer;
use App\Models\Trainee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rules\Password as PasswordRule;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $baseRules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', PasswordRule::min(8)],
            'phone' => ['nullable', 'string', 'max:50'],
            'role' => ['required', 'in:trainer,trainee'],
        ];

        $role = $request->input('role');

        $trainerRules = [
            'specialization' => ['nullable', 'string', 'max:255'],
            'experience_years' => ['nullable', 'integer', 'min:0', 'max:80'],
            'certification' => ['nullable', 'string', 'max:255'],
            'bio' => ['nullable', 'string'],
        ];

        $traineeRules = [
            'trainer_id' => ['nullable', 'integer', 'exists:trainers,id'],
            'current_weight' => ['nullable', 'numeric', 'min:0', 'max:999'],
            'target_weight' => ['nullable', 'numeric', 'min:0', 'max:999'],
            'height' => ['nullable', 'numeric', 'min:0', 'max:999'],
            'age' => ['nullable', 'integer', 'min:0', 'max:120'],
            'gender' => ['nullable', 'in:male,female,other'],
            'goal' => ['nullable', 'in:weight_loss,muscle_gain,maintenance'],
            'starting_date' => ['nullable', 'date'],
        ];

        $rules = $baseRules;
        if ($role === 'trainer') $rules = array_merge($rules, $trainerRules);
        if ($role === 'trainee') $rules = array_merge($rules, $traineeRules);

        $validated = $request->validate($rules);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
            'role' => $validated['role'],
        ]);

        if ($user->role === 'trainer') {
            Trainer::create([
                'user_id' => $user->id,
                'specialization' => $validated['specialization'] ?? null,
                'experience_years' => $validated['experience_years'] ?? 0,
                'certification' => $validated['certification'] ?? null,
                'bio' => $validated['bio'] ?? null,
                'rating' => 0,
            ]);
        } else {
            Trainee::create([
                'user_id' => $user->id,
                'trainer_id' => $validated['trainer_id'] ?? null,
                'current_weight' => $validated['current_weight'] ?? null,
                'target_weight' => $validated['target_weight'] ?? null,
                'height' => $validated['height'] ?? null,
                'age' => $validated['age'] ?? null,
                'gender' => $validated['gender'] ?? null,
                'goal' => $validated['goal'] ?? 'maintenance',
                'starting_date' => $validated['starting_date'] ?? null,
            ]);
        }

        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user->load(['trainerProfile', 'traineeProfile']),
        ], 201);
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Optional: revoke old tokens
        // $user->tokens()->delete();

        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user->load(['trainerProfile', 'traineeProfile']),
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json(['message' => 'Logged out']);
    }

    public function forgotPassword(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $status = Password::sendResetLink($validated);

        return response()->json([
            'status' => $status,
        ]);
    }

    public function resetPassword(Request $request)
    {
        $validated = $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', PasswordRule::min(8)],
        ]);

        $status = Password::reset(
            $validated,
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();

                $user->tokens()->delete(); // force re-login
            }
        );

        return response()->json([
            'status' => $status,
        ]);
    }
}
