<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\AlertQueue;
use App\Models\Donation;
use App\Models\Streamer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class StreamerDashboardController extends Controller
{
    /**
     * Dashboard utama streamer
     */
    public function index(): View|RedirectResponse
    {
        $user = Auth::user();

        // Jika belum punya profil streamer, redirect ke setup
        if (!$user->streamer) {
            return redirect()->route('streamer.setup');
        }

        $streamer  = $user->streamer;
        $stats     = $streamer->buildStats();

        // Donasi terbaru (50 terakhir)
        $donations = $streamer->donations()
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        // Ringkasan harian 7 hari terakhir
        $dailySummary = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dailySummary[] = [
                'date'   => $date->format('d M'),
                'total'  => $streamer->donations()
                    ->whereDate('created_at', $date->toDateString())
                    ->sum('amount'),
                'count'  => $streamer->donations()
                    ->whereDate('created_at', $date->toDateString())
                    ->count(),
            ];
        }

        return view('streamer.dashboard', compact('streamer', 'stats', 'donations', 'dailySummary'));
    }

    /**
     * Form setup profil streamer baru
     */
    public function setup(): View|RedirectResponse
    {
        $user = Auth::user();

        if ($user->streamer) {
            return redirect()->route('streamer.dashboard');
        }

        return view('streamer.setup');
    }

    /**
     * Simpan setup profil streamer baru
     */
    public function storeSetup(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if ($user->streamer) {
            return redirect()->route('streamer.dashboard');
        }

        $validated = $request->validate([
            'display_name' => ['required', 'string', 'max:60'],
            'slug'         => ['required', 'string', 'max:40', 'unique:streamers,slug', 'regex:/^[a-z0-9\-]+$/'],
            'bio'          => ['nullable', 'string', 'max:200'],
        ], [
            'slug.unique'  => 'Slug sudah digunakan. Pilih nama lain.',
            'slug.regex'   => 'Slug hanya boleh huruf kecil, angka, dan tanda hubung (-).',
        ]);

        Streamer::create([
            'user_id'      => $user->id,
            'slug'         => $validated['slug'],
            'display_name' => $validated['display_name'],
            'api_key'      => Streamer::generateApiKey(),
            'bio'          => $validated['bio'] ?? null,
        ]);

        ActivityLog::log(
            action: 'streamer.setup',
            description: "Profil streamer {$validated['display_name']} dibuat",
            userId: $user->id,
        );

        return redirect()->route('streamer.dashboard')
            ->with('success', 'Profil streamer berhasil dibuat!');
    }

    /**
     * Halaman settings streamer
     */
    public function settings(): View|RedirectResponse
    {
        $user = Auth::user();

        if (!$user->streamer) {
            return redirect()->route('streamer.setup');
        }

        $streamer = $user->streamer;
        return view('streamer.settings', compact('streamer'));
    }

    /**
     * Simpan perubahan settings
     */
    public function updateSettings(Request $request): RedirectResponse
    {
        $user     = Auth::user();
        $streamer = $user->streamer;

        if (!$streamer) {
            return redirect()->route('streamer.setup');
        }

        $validated = $request->validate([
            'display_name'         => ['required', 'string', 'max:60'],
            'bio'                  => ['nullable', 'string', 'max:200'],
            'min_donation'         => ['required', 'integer', 'min:100', 'max:1000000'],
            'alert_duration'       => ['required', 'integer', 'min:3000', 'max:30000'],
            'alert_theme'          => ['required', 'in:default,minimal,neon,retro,fire,ice'],
            'yt_enabled'           => ['boolean'],
            'sound_enabled'        => ['boolean'],
            'notification_sound_preset' => ['nullable', 'string', 'in:ding,coin,whoosh,__custom__'],
            'avatar'               => ['nullable', 'image', 'max:2048'],
            'sound_file'           => ['nullable', 'file', 'mimes:mp3,wav,ogg', 'max:5120'],
            'delete_sound'         => ['nullable', 'in:0,1'],
            'milestone_title'      => ['required', 'string', 'max:80'],
            'milestone_target'     => ['required', 'integer', 'min:1000'],
            'milestone_reset'      => ['boolean'],
            'leaderboard_title'    => ['required', 'string', 'max:80'],
            'leaderboard_count'    => ['required', 'integer', 'min:3', 'max:20'],
            'is_accepting_donation'=> ['boolean'],
            'thank_you_message'    => ['required', 'string', 'max:200'],
        ]);

        // Handle avatar upload
        if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
            // Delete old avatar if exists
            if ($streamer->avatar) {
                Storage::disk('public')->delete($streamer->avatar);
            }
            $ext  = $request->file('avatar')->getClientOriginalExtension();
            $path = $request->file('avatar')->storeAs('avatars', $streamer->id . '.' . $ext, 'public');
            $streamer->avatar = $path;
        }

        // Handle sound file upload / delete
        $existingIsCustom   = $streamer->notification_sound
            && !in_array($streamer->notification_sound, ['ding', 'coin', 'whoosh']);
        $submittedPreset    = $request->input('notification_sound_preset', 'ding');
        $userChosePreset    = in_array($submittedPreset, ['ding', 'coin', 'whoosh']);

        if ($request->input('delete_sound') === '1' && $existingIsCustom) {
            // User clicked delete on the custom badge
            Storage::disk('public')->delete($streamer->notification_sound);
            $notificationSound = 'ding';
        } elseif ($request->hasFile('sound_file') && $request->file('sound_file')->isValid()) {
            // New file uploaded — replace old custom if any
            if ($existingIsCustom) {
                Storage::disk('public')->delete($streamer->notification_sound);
            }
            $ext  = $request->file('sound_file')->getClientOriginalExtension();
            $path = $request->file('sound_file')->storeAs('sounds', $streamer->id . '.' . $ext, 'public');
            $notificationSound = $path;
        } elseif ($userChosePreset) {
            // User explicitly selected ding/coin/whoosh — delete old custom file and switch
            if ($existingIsCustom) {
                Storage::disk('public')->delete($streamer->notification_sound);
            }
            $notificationSound = $submittedPreset;
        } elseif ($existingIsCustom) {
            // Custom badge still active, no new file, no delete — preserve existing path
            $notificationSound = $streamer->notification_sound;
        } else {
            $notificationSound = 'ding';
        }

        $streamer->update([
            'display_name'          => $validated['display_name'],
            'bio'                   => $validated['bio'] ?? null,
            'min_donation'          => $validated['min_donation'],
            'alert_duration'        => $validated['alert_duration'],
            'alert_theme'           => $validated['alert_theme'],
            'yt_enabled'            => $request->boolean('yt_enabled'),
            'sound_enabled'         => $request->boolean('sound_enabled'),
            'notification_sound'    => $notificationSound,
            'milestone_title'       => $validated['milestone_title'],
            'milestone_target'      => $validated['milestone_target'],
            'milestone_reset'       => $request->boolean('milestone_reset'),
            'leaderboard_title'     => $validated['leaderboard_title'],
            'leaderboard_count'     => $validated['leaderboard_count'],
            'is_accepting_donation' => $request->boolean('is_accepting_donation'),
            'thank_you_message'     => $validated['thank_you_message'],
        ]);

        // Save avatar separately if updated
        if ($streamer->isDirty('avatar')) {
            $streamer->save();
        }

        ActivityLog::log(
            action: 'streamer.settings.updated',
            description: "Settings streamer {$streamer->display_name} diperbarui",
            streamerId: $streamer->id,
        );

        return back()->with('success', 'Settings berhasil disimpan!');
    }

    /**
     * Regenerate API key streamer
     */
    public function regenerateApiKey(): RedirectResponse
    {
        $user     = Auth::user();
        $streamer = $user->streamer;

        if (!$streamer) {
            return redirect()->route('streamer.setup');
        }

        $streamer->update(['api_key' => Streamer::generateApiKey()]);

        ActivityLog::log(
            action: 'streamer.apikey.regenerated',
            description: "API key streamer {$streamer->display_name} di-regenerate",
            streamerId: $streamer->id,
        );

        return back()->with('success', 'API key berhasil di-regenerate! Update URL widget OBS kamu.');
    }

    /**
     * Kirim test alert ke SSE stream tanpa menyimpan ke database donations
     */
    public function testAlert(Request $request): JsonResponse
    {
        $user     = Auth::user();
        $streamer = $user->streamer;

        if (!$streamer) {
            return response()->json(['error' => 'Profil streamer tidak ditemukan.'], 404);
        }

        // Nama & pesan random untuk test
        $names    = ['Budi Santoso', 'Siti Rahayu', 'Ahmad Fauzi', 'Dewi Pratiwi', 'Rizky Maulana'];
        $messages = ['Semangat streamnya!', 'GG streamer!', 'Keep it up!', 'Mantap kali bang!', 'Halo dari penonton setia!'];
        $emojis   = ['🎉', '💝', '🔥', '👏', '🌟'];
        $amounts  = [5000, 10000, 25000, 50000, 100000];

        $name    = $names[array_rand($names)];
        $message = $messages[array_rand($messages)];
        $emoji   = $emojis[array_rand($emojis)];
        $amount  = $amounts[array_rand($amounts)];

        // Dapatkan seq berikutnya
        $lastSeq = AlertQueue::where('streamer_id', $streamer->id)->max('seq') ?? 0;
        $nextSeq = $lastSeq + 1;

        // Buat payload fake
        $payload = [
            'id'        => 0,
            'seq'       => $nextSeq,
            'name'      => $name,
            'amount'    => $amount,
            'emoji'     => $emoji,
            'msg'       => $message,
            'ytUrl'     => null,
            'ytEnabled' => false,
            'duration'  => $streamer->alert_duration,
            'time'      => now()->toIso8601String(),
            'is_test'   => true,
        ];

        // Insert langsung dengan DB untuk bypass FK constraint (donation_id nullable)
        \Illuminate\Support\Facades\DB::table('alert_queues')->insert([
            'streamer_id' => $streamer->id,
            'donation_id' => null,
            'seq'         => $nextSeq,
            'payload'     => json_encode($payload),
            'expires_at'  => now()->addMinutes(5)->toDateTimeString(),
            'created_at'  => now()->toDateTimeString(),
        ]);

        return response()->json([
            'ok'     => true,
            'name'   => $name,
            'amount' => $amount,
            'emoji'  => $emoji,
        ]);
    }
}
