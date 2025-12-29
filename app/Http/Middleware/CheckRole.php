<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Usage: ->middleware('role:trainer') or ->middleware('role:trainee')
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();

        if (!$user || $user->role !== $role) {
            return response()->json([
                'message' => 'Forbidden. Role required: ' . $role,
            ], 403);
        }

        return $next($request);
    }
}
