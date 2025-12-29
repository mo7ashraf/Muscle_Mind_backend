<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // GET /notifications
    public function index(Request $request)
    {
        $items = Notification::where('user_id', $request->user()->id)
            ->orderByDesc('id')
            ->paginate(50);

        return response()->json($items);
    }

    // PUT /notifications/{id}/read
    public function markRead(Request $request, int $id)
    {
        $n = Notification::where('user_id', $request->user()->id)->findOrFail($id);
        $n->read_at = now();
        $n->save();

        return response()->json(['notification' => $n]);
    }
}
