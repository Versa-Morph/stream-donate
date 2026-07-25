<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessDonationJob;
use App\Models\ActivityLog;
use App\Models\Donation;
use App\Services\AlertFailureService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminAlertFailureController extends Controller
{
    public function __construct(
        private readonly AlertFailureService $alertFailureService
    ) {}

    public function index(): View
    {
        $failures = $this->alertFailureService->unresolved()->map(function ($log) {
            $donationId = $log->payload['donation_id'] ?? null;
            return [
                'log' => $log,
                'donation' => $donationId ? Donation::with('streamer')->find($donationId) : null,
                'error' => $log->payload['error'] ?? null,
            ];
        });

        return view('admin.alert-failures', compact('failures'));
    }

    public function retry(Donation $donation): RedirectResponse
    {
        try {
            ProcessDonationJob::dispatchSync($donation);
        } catch (\Throwable $e) {
            return back()->withErrors(['retry' => 'Retry gagal: ' . $e->getMessage()]);
        }

        ActivityLog::log(
            action: 'donation.alert_retried',
            description: "Alert donasi #{$donation->id} berhasil di-retry oleh admin",
            userId: Auth::id(),
            streamerId: $donation->streamer_id,
            payload: ['donation_id' => $donation->id],
        );

        return back()->with('success', 'Alert berhasil di-retry.');
    }
}
