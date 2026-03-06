<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessDonationJob;
use App\Models\ActivityLog;
use App\Models\Donation;
use App\Models\Streamer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DonationController extends Controller
{
    /**
     * Tampilkan form donasi publik untuk streamer berdasarkan slug
     */
    public function show(string $slug): View
    {
        $streamer = Streamer::where('slug', $slug)->firstOrFail();

        abort_unless($streamer->is_accepting_donation, 404, 'Streamer sedang tidak menerima donasi.');

        return view('donate.show', compact('streamer'));
    }

    /**
     * Proses donasi baru (POST dari form publik)
     */
    public function store(Request $request, string $slug): JsonResponse
    {
        $streamer = Streamer::where('slug', $slug)->firstOrFail();

        abort_unless($streamer->is_accepting_donation, 403, 'Streamer sedang tidak menerima donasi.');

        // Validasi input
        $validated = $request->validate([
            'name'   => ['required', 'string', 'max:60'],
            'amount' => ['required', 'integer', 'min:' . $streamer->min_donation],
            'emoji'  => ['nullable', 'string', 'max:10'],
            'msg'    => ['nullable', 'string', 'max:200'],
            'yt_url' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^https?:\/\/(www\.)?(youtube\.com|youtu\.be)\//i',
            ],
        ], [
            'name.required'  => 'Nama wajib diisi.',
            'amount.required' => 'Jumlah donasi wajib diisi.',
            'amount.min'     => 'Minimum donasi adalah Rp ' . number_format($streamer->min_donation, 0, ',', '.'),
            'yt_url.regex'   => 'URL YouTube tidak valid. Gunakan youtube.com atau youtu.be',
        ]);

        // Sanitasi
        $name   = strip_tags($validated['name']);
        $msg    = isset($validated['msg']) ? strip_tags($validated['msg']) : null;
        $ytUrl  = ($streamer->yt_enabled && isset($validated['yt_url'])) ? $validated['yt_url'] : null;
        $emoji  = $validated['emoji'] ?? '💝';

        // Simpan donasi ke DB
        $donation = Donation::create([
            'streamer_id' => $streamer->id,
            'name'        => $name,
            'amount'      => (int) $validated['amount'],
            'emoji'       => $emoji,
            'message'     => $msg,
            'yt_url'      => $ytUrl,
            'ip_address'  => $request->ip(),
        ]);

        // Dispatch synchronous — langsung proses tanpa queue worker
        ProcessDonationJob::dispatchSync($donation);

        // Log activity
        ActivityLog::log(
            action: 'donation.create',
            description: "{$name} berdonasi Rp " . number_format($donation->amount, 0, ',', '.'),
            streamerId: $streamer->id,
            payload: ['donation_id' => $donation->id]
        );

        return response()->json([
            'success' => true,
            'message' => $streamer->thank_you_message,
            'id'      => $donation->id,
        ]);
    }
}
