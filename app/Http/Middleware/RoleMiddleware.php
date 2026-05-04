<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // ตรวจสอบว่า Role ของคนที่ Login ตรงกับที่ Route ต้องการหรือไม่
        if ($request->user()->role !== $role) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. You do not have permission to access this endpoint.'
            ], 403);
        }

        return $next($request);
    }
}