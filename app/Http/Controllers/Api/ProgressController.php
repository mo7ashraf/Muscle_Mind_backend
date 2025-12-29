<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Measurement;
use App\Models\ProgressPhoto;
use Illuminate\Http\Request;

class ProgressController extends Controller
{
    // GET /trainee/progress
    public function progress(Request $request)
    {
        $trainee = $request->user()->traineeProfile;
        if (!$trainee) return response()->json(['message' => 'Trainee profile not found'], 404);

        $items = ProgressPhoto::where('trainee_id', $trainee->id)
            ->orderByDesc('taken_at')
            ->paginate(20);

        return response()->json($items);
    }

    // POST /trainee/progress/photos
    public function uploadProgressPhotos(Request $request)
    {
        $trainee = $request->user()->traineeProfile;
        if (!$trainee) return response()->json(['message' => 'Trainee profile not found'], 404);

        $validated = $request->validate([
            'front_image' => ['nullable', 'image', 'max:8192'],
            'back_image' => ['nullable', 'image', 'max:8192'],
            'side_image' => ['nullable', 'image', 'max:8192'],
            'weight' => ['nullable', 'numeric', 'min:0', 'max:999'],
            'notes' => ['nullable', 'string'],
            'taken_at' => ['nullable', 'date'],
        ]);

        $folder = 'progress/' . $trainee->id;

        $front = $request->file('front_image')?->store($folder, 'public');
        $back  = $request->file('back_image')?->store($folder, 'public');
        $side  = $request->file('side_image')?->store($folder, 'public');

        $photo = ProgressPhoto::create([
            'trainee_id' => $trainee->id,
            'front_image' => $front,
            'back_image' => $back,
            'side_image' => $side,
            'weight' => $validated['weight'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'taken_at' => $validated['taken_at'] ?? now(),
        ]);

        return response()->json(['progress' => $photo], 201);
    }

    // POST /trainee/measurements
    public function addMeasurement(Request $request)
    {
        $trainee = $request->user()->traineeProfile;
        if (!$trainee) return response()->json(['message' => 'Trainee profile not found'], 404);

        $validated = $request->validate([
            'weight' => ['nullable', 'numeric', 'min:0', 'max:999'],
            'chest' => ['nullable', 'numeric', 'min:0', 'max:999'],
            'waist' => ['nullable', 'numeric', 'min:0', 'max:999'],
            'hips' => ['nullable', 'numeric', 'min:0', 'max:999'],
            'arms' => ['nullable', 'numeric', 'min:0', 'max:999'],
            'thighs' => ['nullable', 'numeric', 'min:0', 'max:999'],
            'measured_at' => ['nullable', 'date'],
        ]);

        $m = Measurement::create([
            'trainee_id' => $trainee->id,
            'weight' => $validated['weight'] ?? null,
            'chest' => $validated['chest'] ?? null,
            'waist' => $validated['waist'] ?? null,
            'hips' => $validated['hips'] ?? null,
            'arms' => $validated['arms'] ?? null,
            'thighs' => $validated['thighs'] ?? null,
            'measured_at' => $validated['measured_at'] ?? now(),
        ]);

        // Update current_weight snapshot (optional convenience)
        if (!is_null($m->weight)) {
            $trainee->current_weight = $m->weight;
            $trainee->save();
        }

        return response()->json(['measurement' => $m], 201);
    }

    // GET /trainee/measurements/history
    public function measurementHistory(Request $request)
    {
        $trainee = $request->user()->traineeProfile;
        if (!$trainee) return response()->json(['message' => 'Trainee profile not found'], 404);

        $items = Measurement::where('trainee_id', $trainee->id)
            ->orderByDesc('measured_at')
            ->paginate(50);

        return response()->json($items);
    }
}
