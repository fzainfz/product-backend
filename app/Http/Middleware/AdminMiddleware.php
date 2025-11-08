<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Only allow admin users to proceed.
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $user = Auth::guard('api')->user();

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized. Please log in.'
                ], 401);
            }

            if (!$user->is_admin) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Forbidden. Admin access only.'
                ], 403);
            }

            return $next($request);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to verify admin access.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
