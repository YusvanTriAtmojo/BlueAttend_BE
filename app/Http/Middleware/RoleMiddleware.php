<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = Auth::guard('api')->user();

        if (!$user || !$user->role) {
            return response()->json([
                'message' => 'Pengguna tidak memiliki akses',
                'status_code' => 401,
                'data' => null
            ], 401);
        }
        if (!in_array($user->role->nama_role, $roles)) {
            return response()->json([
                'message' => 'Unauthorized in ' . implode(', ', $roles),
                'status_code' => 403,
                'data' => null
            ], 403);
        }

        return $next($request);
    }
}
