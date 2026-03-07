<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\EnsureAdmin::class,
            'streamer' => \App\Http\Middleware\EnsureStreamer::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        // ── Token CSRF expired ──
        $exceptions->render(function (TokenMismatchException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sesi Anda telah berakhir. Mohon muat ulang halaman dan coba lagi.',
                ], 419);
            }
            return redirect()->back()
                ->withInput($request->except('_token'))
                ->with('error', 'Sesi Anda telah berakhir. Mohon coba lagi.');
        });

        // ── Rate limit ──
        $exceptions->render(function (ThrottleRequestsException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terlalu banyak permintaan. Mohon tunggu sebentar sebelum mencoba lagi.',
                ], 429);
            }
            return response()->view('errors.429', [], 429);
        });

        // ── Model tidak ditemukan → 404 ──
        $exceptions->render(function (ModelNotFoundException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data yang diminta tidak ditemukan.',
                ], 404);
            }
            return response()->view('errors.404', [], 404);
        });

        // ── HTTP Exception (401, 403, 404, dll) ──
        $exceptions->render(function (HttpException $e, $request) {
            $status  = $e->getStatusCode();
            $message = resolveHttpMessage($status, $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                ], $status);
            }

            $view = 'errors.' . $status;
            if (view()->exists($view)) {
                return response()->view($view, ['exception' => $e], $status);
            }

            return response()->view('errors.generic', [
                'status'  => $status,
                'message' => $message,
            ], $status);
        });

        // ── Error tidak terduga (500) ──
        $exceptions->render(function (\Throwable $e, $request) {
            // Validation exception — biarkan Laravel handle
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                return null;
            }

            Log::error('Unhandled exception', [
                'url'    => $request->fullUrl(),
                'method' => $request->method(),
                'error'  => $e->getMessage(),
                'file'   => $e->getFile() . ':' . $e->getLine(),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan pada server. Tim kami sedang menangani masalah ini.',
                ], 500);
            }

            if (config('app.debug')) {
                return null; // biarkan Ignition/Whoops tampil saat debug
            }

            return response()->view('errors.500', [], 500);
        });

    })->create();

/**
 * Konversi HTTP status code ke pesan yang user-friendly
 */
function resolveHttpMessage(int $status, string $originalMessage = ''): string
{
    if (
        $originalMessage
        && strlen($originalMessage) > 0
        && strlen($originalMessage) < 200
        && !str_contains($originalMessage, '/')
        && !str_contains($originalMessage, '\\')
    ) {
        return $originalMessage;
    }

    return match ($status) {
        400 => 'Permintaan tidak valid.',
        401 => 'Anda perlu login untuk mengakses halaman ini.',
        403 => 'Anda tidak memiliki akses ke halaman ini.',
        404 => 'Halaman yang Anda cari tidak ditemukan.',
        405 => 'Metode permintaan tidak diizinkan.',
        419 => 'Sesi Anda telah berakhir. Muat ulang halaman dan coba lagi.',
        422 => 'Data yang dikirim tidak valid.',
        429 => 'Terlalu banyak permintaan. Coba lagi dalam beberapa saat.',
        500 => 'Terjadi kesalahan pada server. Tim kami sedang menangani.',
        503 => 'Layanan sedang dalam pemeliharaan. Coba lagi nanti.',
        default => 'Terjadi kesalahan. Mohon coba lagi.',
    };
}
