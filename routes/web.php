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
    Route::post('/settings', [StreamerDashboardController::class, 'updateSettings'])
        ->middleware('throttle:settings-update')
        ->name('settings.update');

    // Regenerate API key
    Route::post('/regenerate-key', [StreamerDashboardController::class, 'regenerateApiKey'])
        ->middleware('throttle:api-key-regen')
        ->name('regenerate-key');

    // Test Alert — kirim fake donation ke SSE tanpa simpan ke DB
    Route::post('/test-alert', [StreamerDashboardController::class, 'testAlert'])
        ->middleware('throttle:test-alert')
        ->name('test-alert');

    // Heatmap data AJAX — navigasi bulan prev/next
    Route::get('/heatmap-data', [StreamerDashboardController::class, 'heatmapData'])
        ->middleware('throttle:heatmap')
        ->name('heatmap-data');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports');
    Route::get('/reports/export/csv', [ReportController::class, 'exportCsv'])
        ->middleware('throttle:report-export')
        ->name('reports.csv');
    Route::get('/reports/export/pdf', [ReportController::class, 'exportPdf'])
        ->middleware('throttle:report-export')
        ->name('reports.pdf');

    // Banned words — streamer's own custom words (AJAX)
    Route::get('/banned-words',              [StreamerBannedWordController::class, 'index'])->name('banned-words.index');
    Route::post('/banned-words',             [StreamerBannedWordController::class, 'store'])->name('banned-words.store');
    Route::delete('/banned-words/{bannedWord}', [StreamerBannedWordController::class, 'destroy'])->name('banned-words.destroy');

    // OBS Canvas Editor
    Route::get('/obs-canvas',  [ObsCanvasController::class, 'editor'])->name('obs-canvas');
    Route::post('/obs-canvas', [ObsCanvasController::class, 'save'])
        ->middleware('throttle:settings-update')
        ->name('obs-canvas.save');

    // Widget Studio
    Route::get('/widgets',  [StreamerDashboardController::class, 'widgets'])->name('widgets');
    Route::post('/widgets', [StreamerDashboardController::class, 'saveWidgets'])
        ->middleware('throttle:settings-update')
        ->name('widgets.save');
    Route::post('/widgets/alert-settings', [StreamerDashboardController::class, 'saveAlertSettings'])
        ->middleware('throttle:settings-update')
        ->name('widgets.alert-settings');
    Route::post('/widgets/milestone-settings', [StreamerDashboardController::class, 'saveMilestoneSettings'])
        ->middleware('throttle:settings-update')
        ->name('widgets.milestone-settings');
    Route::post('/widgets/leaderboard-settings', [StreamerDashboardController::class, 'saveLeaderboardSettings'])
        ->middleware('throttle:settings-update')
        ->name('widgets.leaderboard-settings');

    // Subathon
    Route::get('/subathon', [StreamerDashboardController::class, 'subathon'])->name('subathon');
    Route::post('/subathon', [StreamerDashboardController::class, 'saveSubathonSettings'])
        ->middleware('throttle:settings-update')
        ->name('subathon.save');
    Route::post('/subathon/reset-timer', [StreamerDashboardController::class, 'resetSubathonTimer'])
        ->middleware('throttle:settings-update')
        ->name('subathon.reset-timer');
    Route::post('/subathon/add-time', [StreamerDashboardController::class, 'addSubathonTimeManual'])
        ->middleware('throttle:settings-update')
        ->name('subathon.add-time');
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
    Route::post('/users', [AdminController::class, 'storeUser'])
        ->middleware('throttle:admin-actions')
        ->name('users.store');
    Route::post('/users/{user}/toggle', [AdminController::class, 'toggleUser'])
        ->middleware('throttle:admin-actions')
        ->name('users.toggle');
    Route::post('/users/{user}/reset-password', [AdminController::class, 'resetPassword'])
        ->middleware('throttle:admin-actions')
        ->name('users.reset-password');

    // Semua donasi
    Route::get('/donations', [AdminController::class, 'donations'])->name('donations');
    Route::delete('/donations/{donation}', [AdminController::class, 'deleteDonation'])
        ->middleware('throttle:admin-actions')
        ->name('donations.delete');

    // Activity logs
    Route::get('/logs', [AdminController::class, 'logs'])->name('logs');

    // Impersonate — /stop harus SEBELUM /{user} agar tidak ditangkap sebagai model binding
    Route::post('/impersonate/{user}', [AdminController::class, 'impersonate'])
        ->middleware('throttle:admin-actions')
        ->name('impersonate');

    // Banned words — global list management
    Route::get('/banned-words',                 [AdminBannedWordController::class, 'index'])->name('banned-words.index');
    Route::post('/banned-words',                [AdminBannedWordController::class, 'store'])
        ->middleware('throttle:admin-actions')
        ->name('banned-words.store');
    Route::delete('/banned-words/{bannedWord}', [AdminBannedWordController::class, 'destroy'])
        ->middleware('throttle:admin-actions')
        ->name('banned-words.destroy');
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
Route::get('/{slug}/qr', [QrController::class, 'show'])
    ->middleware('throttle:qr-code')
    ->name('qr.show');

// SSE endpoint (diakses dari OBS widget / browser)
Route::get('/{slug}/sse', [SseController::class, 'stream'])
    ->middleware('throttle:sse')
    ->name('sse.stream');
Route::get('/{slug}/stats', [SseController::class, 'stats'])
    ->middleware('throttle:stats-api')
    ->name('sse.stats');

// OBS Widgets (tanpa auth, diakses dari OBS Browser Source)
Route::get('/{slug}/obs/overlay',     [ObsController::class, 'overlay'])
    ->middleware('throttle:obs-widget')
    ->name('obs.overlay');
Route::get('/{slug}/obs/leaderboard', [ObsController::class, 'leaderboard'])
    ->middleware('throttle:obs-widget')
    ->name('obs.leaderboard');
Route::get('/{slug}/obs/milestone',   [ObsController::class, 'milestone'])
    ->middleware('throttle:obs-widget')
    ->name('obs.milestone');
Route::get('/{slug}/obs/qr',          [QrController::class, 'obsWidget'])
    ->middleware('throttle:obs-widget')
    ->name('obs.qr');
Route::get('/{slug}/obs/canvas',      [ObsCanvasController::class, 'render'])
    ->middleware('throttle:obs-widget')
    ->name('obs.canvas');
Route::get('/{slug}/obs/subathon',    [ObsController::class, 'subathon'])
    ->middleware('throttle:obs-widget')
    ->name('obs.subathon');
Route::get('/{slug}/obs/running-text', [ObsController::class, 'runningText'])
    ->middleware('throttle:obs-widget')
    ->name('obs.running-text');
