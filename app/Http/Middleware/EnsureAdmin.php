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
                return response()->json(['message' => 'Akses ditolak. Hanya admin yang diperbolehkan.'], 403);
            }
            abort(403, 'Akses ditolak. Hanya admin yang diperbolehkan.');
        }

        return $next($request);
    }
}
