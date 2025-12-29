<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Challenge;
use App\Models\TraineeChallenge;
use Illuminate\Http\Request;

class ChallengeController extends Controller
{
    // GET /challenges
    public function index(Request $request)
    {
        $query = Challenge::query();

        if ($request->boolean('active_only', true)) {
            $query->where('is_active', true);
        }

        return response()->json($query->orderByDesc('id')->paginate(30));
    }

    // POST /challenges/join/{id} (trainee)
    public function join(Request $request, int $id)
    {
        $trainee = $request->user()->traineeProfile;
        if (!$trainee) return response()->json(['message' => 'Trainee profile not found'], 404);

        $challenge = Challenge::where('is_active', true)->findOrFail($id);

        $row = TraineeChallenge::firstOrCreate(
            ['trainee_id' => $trainee->id, 'challenge_id' => $challenge->id],
            ['start_date' => now()->toDateString(), 'status' => 'ongoing', 'completed_days' => 0]
        );

        return response()->json(['trainee_challenge' => $row->load('challenge')], 201);
    }

    // POST /challenges/checkin/{id} (trainee)
    public function checkin(Request $request, int $id)
    {
        $trainee = $request->user()->traineeProfile;
        if (!$trainee) return response()->json(['message' => 'Trainee profile not found'], 404);

        $row = TraineeChallenge::with('challenge')
            ->where('trainee_id', $trainee->id)
            ->where('challenge_id', $id)
            ->firstOrFail();

        if ($row->status !== 'ongoing') {
            return response()->json(['message' => 'Challenge not ongoing'], 400);
        }

        $today = now()->toDateString();

        // Prevent duplicate same-day check-in
        if ($row->last_check_in === $today) {
            return response()->json(['message' => 'Already checked in today'], 409);
        }

        $row->completed_days += 1;
        $row->last_check_in = $today;

        if ($row->completed_days >= ($row->challenge->duration_days ?? 30)) {
            $row->status = 'completed';
        }

        $row->save();

        return response()->json(['trainee_challenge' => $row]);
    }

    // GET /trainee/challenges (trainee)
    public function myChallenges(Request $request)
    {
        $trainee = $request->user()->traineeProfile;
        if (!$trainee) return response()->json(['message' => 'Trainee profile not found'], 404);

        $items = TraineeChallenge::with('challenge')
            ->where('trainee_id', $trainee->id)
            ->orderByDesc('id')
            ->paginate(30);

        return response()->json($items);
    }
}
