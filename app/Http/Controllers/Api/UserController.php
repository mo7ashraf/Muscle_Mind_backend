<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function me(Request $request)
    {
        return response()->json([
            'user' => $request->user()->load(['trainerProfile', 'traineeProfile']),
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:50'],

            // trainer profile fields
            'specialization' => ['sometimes', 'nullable', 'string', 'max:255'],
            'experience_years' => ['sometimes', 'nullable', 'integer', 'min:0', 'max:80'],
            'certification' => ['sometimes', 'nullable', 'string', 'max:255'],
            'bio' => ['sometimes', 'nullable', 'string'],

            // trainee profile fields
            'trainer_id' => ['sometimes', 'nullable', 'integer', 'exists:trainers,id'],
            'current_weight' => ['sometimes', 'nullable', 'numeric', 'min:0', 'max:999'],
            'target_weight' => ['sometimes', 'nullable', 'numeric', 'min:0', 'max:999'],
            'height' => ['sometimes', 'nullable', 'numeric', 'min:0', 'max:999'],
            'age' => ['sometimes', 'nullable', 'integer', 'min:0', 'max:120'],
            'gender' => ['sometimes', 'nullable', 'in:male,female,other'],
            'goal' => ['sometimes', 'nullable', 'in:weight_loss,muscle_gain,maintenance'],
            'starting_date' => ['sometimes', 'nullable', 'date'],
        ]);

        $user->fill([
            'name' => $validated['name'] ?? $user->name,
            'phone' => array_key_exists('phone', $validated) ? $validated['phone'] : $user->phone,
        ])->save();

        if ($user->role === 'trainer' && $user->trainerProfile) {
            $user->trainerProfile->fill([
                'specialization' => $validated['specialization'] ?? $user->trainerProfile->specialization,
                'experience_years' => $validated['experience_years'] ?? $user->trainerProfile->experience_years,
                'certification' => $validated['certification'] ?? $user->trainerProfile->certification,
                'bio' => $validated['bio'] ?? $user->trainerProfile->bio,
            ])->save();
        }

        if ($user->role === 'trainee' && $user->traineeProfile) {
            $user->traineeProfile->fill([
                'trainer_id' => array_key_exists('trainer_id', $validated) ? $validated['trainer_id'] : $user->traineeProfile->trainer_id,
                'current_weight' => $validated['current_weight'] ?? $user->traineeProfile->current_weight,
                'target_weight' => $validated['target_weight'] ?? $user->traineeProfile->target_weight,
                'height' => $validated['height'] ?? $user->traineeProfile->height,
                'age' => $validated['age'] ?? $user->traineeProfile->age,
                'gender' => $validated['gender'] ?? $user->traineeProfile->gender,
                'goal' => $validated['goal'] ?? $user->traineeProfile->goal,
                'starting_date' => $validated['starting_date'] ?? $user->traineeProfile->starting_date,
            ])->save();
        }

        return response()->json([
            'user' => $user->fresh()->load(['trainerProfile', 'traineeProfile']),
        ]);
    }

    public function uploadAvatar(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'avatar' => ['required', 'image', 'max:5120'], // 5MB
        ]);

        if ($user->profile_image) {
            Storage::disk('public')->delete($user->profile_image);
        }

        $path = $request->file('avatar')->store('avatars', 'public');

        $user->profile_image = $path;
        $user->save();

        return response()->json([
            'profile_image' => $path,
            'profile_image_url' => asset('storage/' . $path),
        ]);
    }
}
