<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\BannedWordController as AdminBannedWordController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\ObsCanvasController;
use App\Http\Controllers\ObsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PolicyController;
use App\Http\Controllers\QrController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SseController;
use App\Http\Controllers\StreamerDashboardController;
use App\Http\Controllers\Streamer\BannedWordController as StreamerBannedWordController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Auth Routes (Breeze) — HARUS di atas route /{slug}
|--------------------------------------------------------------------------
*/

require __DIR__ . '/auth.php';

/*
|--------------------------------------------------------------------------
| Public Routes — Policies
|--------------------------------------------------------------------------
*/

Route::get('/policies', [PolicyController::class, 'index'])->name('policies.index');
Route::get('/policies/{slug}', [PolicyController::class, 'show'])->name('policies.show');

/*
|--------------------------------------------------------------------------
| Public Routes — Donasi (tanpa auth)
|--------------------------------------------------------------------------
*/

// Halaman utama — landing page / company profile
Route::get('/', function () {
    return view('welcome');
})->name('home');

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

// Redirect /dashboard ke halaman yang sesuai role
Route::get('/dashboard', function () {
    $user = auth()->user();
    if ($user->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('streamer.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Profile (Breeze default)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Setup profil streamer — hanya auth+verified, TIDAK kena middleware 'streamer'
// (karena streamer baru belum punya profil, middleware 'streamer' akan loop)
Route::middleware(['auth', 'verified'])->prefix('streamer')->name('streamer.')->group(function () {
    Route::get('/setup',  [StreamerDashboardController::class, 'setup'])->name('setup');
    Route::post('/setup', [StreamerDashboardController::class, 'storeSetup'])->name('setup.store');
});

/*
|--------------------------------------------------------------------------
| Streamer Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', 'streamer'])->prefix('streamer')->name('streamer.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [StreamerDashboardController::class, 'index'])->name('dashboard');

    // Settings
    Route::get('/settings', [StreamerDashboardController::class, 'settings'])->name('settings');
    Route::post('/settings', [StreamerDashboardController::class, 'updateSettings'])->name('settings.update');

    // Regenerate API key
    Route::post('/regenerate-key', [StreamerDashboardController::class, 'regenerateApiKey'])->name('regenerate-key');

    // Test Alert — kirim fake donation ke SSE tanpa simpan ke DB
    Route::post('/test-alert', [StreamerDashboardController::class, 'testAlert'])->name('test-alert');

    // Heatmap data AJAX — navigasi bulan prev/next
    Route::get('/heatmap-data', [StreamerDashboardController::class, 'heatmapData'])->name('heatmap-data');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports');
    Route::get('/reports/export/csv', [ReportController::class, 'exportCsv'])->name('reports.csv');
    Route::get('/reports/export/pdf', [ReportController::class, 'exportPdf'])->name('reports.pdf');

    // Banned words — streamer's own custom words (AJAX)
    Route::get('/banned-words',              [StreamerBannedWordController::class, 'index'])->name('banned-words.index');
    Route::post('/banned-words',             [StreamerBannedWordController::class, 'store'])->name('banned-words.store');
    Route::delete('/banned-words/{bannedWord}', [StreamerBannedWordController::class, 'destroy'])->name('banned-words.destroy');

    // OBS Canvas Editor
    Route::get('/obs-canvas',  [ObsCanvasController::class, 'editor'])->name('obs-canvas');
    Route::post('/obs-canvas', [ObsCanvasController::class, 'save'])->name('obs-canvas.save');

    // Widget Studio
    Route::get('/widgets',  [StreamerDashboardController::class, 'widgets'])->name('widgets');
    Route::post('/widgets', [StreamerDashboardController::class, 'saveWidgets'])->name('widgets.save');
    Route::post('/widgets/alert-settings', [StreamerDashboardController::class, 'saveAlertSettings'])->name('widgets.alert-settings');

    // Subathon
    Route::get('/subathon', [StreamerDashboardController::class, 'subathon'])->name('subathon');
    Route::post('/subathon', [StreamerDashboardController::class, 'saveSubathonSettings'])->name('subathon.save');
    Route::post('/subathon/reset-timer', [StreamerDashboardController::class, 'resetSubathonTimer'])->name('subathon.reset-timer');
    Route::post('/subathon/add-time', [StreamerDashboardController::class, 'addSubathonTimeManual'])->name('subathon.add-time');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    // Stop impersonate — di luar middleware 'admin' karena user aktif sedang jadi streamer
    Route::post('/impersonate/stop', [AdminController::class, 'stopImpersonate'])->name('impersonate.stop');
});

Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard admin
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // Manajemen user
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/users/create', [AdminController::class, 'createUser'])->name('users.create');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
    Route::post('/users/{user}/toggle', [AdminController::class, 'toggleUser'])->name('users.toggle');
    Route::post('/users/{user}/reset-password', [AdminController::class, 'resetPassword'])->name('users.reset-password');

    // Semua donasi
    Route::get('/donations', [AdminController::class, 'donations'])->name('donations');
    Route::delete('/donations/{donation}', [AdminController::class, 'deleteDonation'])->name('donations.delete');

    // Activity logs
    Route::get('/logs', [AdminController::class, 'logs'])->name('logs');

    // Impersonate — /stop harus SEBELUM /{user} agar tidak ditangkap sebagai model binding
    Route::post('/impersonate/{user}', [AdminController::class, 'impersonate'])->name('impersonate');

    // Banned words — global list management
    Route::get('/banned-words',                 [AdminBannedWordController::class, 'index'])->name('banned-words.index');
    Route::post('/banned-words',                [AdminBannedWordController::class, 'store'])->name('banned-words.store');
    Route::delete('/banned-words/{bannedWord}', [AdminBannedWordController::class, 'destroy'])->name('banned-words.destroy');
});

/*
|--------------------------------------------------------------------------
| Public Slug Routes — HARUS di paling bawah (fallback wildcard)
|--------------------------------------------------------------------------
*/

// Form donasi publik per streamer
Route::get('/{slug}', [DonationController::class, 'show'])->name('donate.show');
Route::post('/{slug}/donate', [DonationController::class, 'store'])
    ->middleware('throttle:donate')
    ->name('donate.store');

// QR Code per streamer (SVG inline)
Route::get('/{slug}/qr', [QrController::class, 'show'])->name('qr.show');

// SSE endpoint (diakses dari OBS widget / browser)
Route::get('/{slug}/sse', [SseController::class, 'stream'])->name('sse.stream');
Route::get('/{slug}/stats', [SseController::class, 'stats'])->name('sse.stats');

// OBS Widgets (tanpa auth, diakses dari OBS Browser Source)
Route::get('/{slug}/obs/overlay',     [ObsController::class, 'overlay'])->name('obs.overlay');
Route::get('/{slug}/obs/leaderboard', [ObsController::class, 'leaderboard'])->name('obs.leaderboard');
Route::get('/{slug}/obs/milestone',   [ObsController::class, 'milestone'])->name('obs.milestone');
Route::get('/{slug}/obs/qr',          [QrController::class, 'obsWidget'])->name('obs.qr');
Route::get('/{slug}/obs/canvas',      [ObsCanvasController::class, 'render'])->name('obs.canvas');
Route::get('/{slug}/obs/subathon',    [ObsController::class, 'subathon'])->name('obs.subathon');
