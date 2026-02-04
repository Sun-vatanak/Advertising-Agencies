<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class isLogin
{
    public function handle(Request $request, Closure $next): Response
    {

        Auth::shouldUse('sanctum');

        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'result' => false,
                'message' => 'You must be logged in',
                'data' => [],
            ], 401);
        }

        return $next($request);
    }
}
