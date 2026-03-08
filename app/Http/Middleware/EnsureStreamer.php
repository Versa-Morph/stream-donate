<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureStreamer
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || !auth()->user()->isStreamer()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Akses ditolak. Hanya streamer yang diperbolehkan.'], 403);
            }
            abort(403, 'Akses ditolak. Hanya streamer yang diperbolehkan.');
        }

        // Pastikan streamer sudah punya profil
        if (!auth()->user()->streamer) {
            return redirect()->route('streamer.setup')
                ->with('warning', 'Lengkapi profil streamer Anda terlebih dahulu.');
        }

        return $next($request);
    }
}
