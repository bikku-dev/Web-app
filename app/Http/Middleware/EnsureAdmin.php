<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized access. Admin privileges required.'], 403);
            }
            return redirect()->route('dashboard')->with('error', 'Access denied. Administrator privileges required.');
        }

        return $next($request);
    }
}
