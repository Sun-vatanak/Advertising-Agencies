<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class isAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user('sanctum');

        // Add this check FIRST
        if (!$user) {
            return response()->json([
                'result' => false,
                'message' => 'Unauthenticated. Please provide a valid token.',
                'data' => [],
            ], 401);
        }

        // Load role if not loaded
        if (!$user->relationLoaded('role')) {
            $user->load('role');
        }

        $role = $user->role;

        // Check if role exists
        if (!$role) {
            return response()->json([
                'result' => false,
                'message' => 'User has no assigned role.',
                'data' => [],
            ], 403);
        }

        // Check if admin
        if ($role->id != 1) {
            return response()->json([
                'result' => false,
                'message' => 'Access denied. Admin only.',
                'data' => [],
            ], 403);
        }

        return $next($request);
    }
}
