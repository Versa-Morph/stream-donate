<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\Streamer;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Laporan donasi streamer
     */
    public function index(Request $request): View
    {
        $streamer = Auth::user()->streamer;
        abort_unless($streamer, 403);

        $dateFrom = $request->input('from', now()->startOfMonth()->toDateString());
        $dateTo   = $request->input('to',   now()->toDateString());

        $baseQuery = $streamer->donations()
            ->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo);

        // Full collection for stats
        $donations = (clone $baseQuery)->orderBy('created_at', 'desc')->get();

        // Paginated for the history table (25 per page), preserving filter params
        $donationsPaginated = (clone $baseQuery)
            ->orderBy('created_at', 'desc')
            ->paginate(25)
            ->withQueryString();

        // Summary stats
        $totalAmount  = $donations->sum('amount');
        $totalCount   = $donations->count();
        $uniqueDonors = $donations->pluck('name')->unique()->count();
        $avgAmount    = $totalCount > 0 ? intdiv($totalAmount, $totalCount) : 0;
        $maxDonation  = $donations->sortByDesc('amount')->first();

        return view('reports.index', compact(
            'streamer', 'donations', 'donationsPaginated', 'dateFrom', 'dateTo',
            'totalAmount', 'totalCount', 'uniqueDonors', 'avgAmount', 'maxDonation'
        ));
    }

    /**
     * Export CSV
     */
    public function exportCsv(Request $request): Response
    {
        $streamer = Auth::user()->streamer;
        abort_unless($streamer, 403);

        $dateFrom = $request->input('from', now()->startOfMonth()->toDateString());
        $dateTo   = $request->input('to',   now()->toDateString());

        $donations = $streamer->donations()
            ->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->orderBy('created_at', 'desc')
            ->get();

        $rows   = [];
        $rows[] = ['No', 'Tanggal', 'Nama', 'Nominal (Rp)', 'Emoji', 'Pesan', 'YouTube URL'];

        foreach ($donations as $i => $d) {
            $rows[] = [
                $i + 1,
                $d->created_at->format('d/m/Y H:i'),
                $d->name,
                $d->amount,
                $d->emoji,
                $d->message ?? '',
                $d->yt_url ?? '',
            ];
        }

        $csv = '';
        foreach ($rows as $row) {
            $csv .= implode(',', array_map(fn ($v) => '"' . str_replace('"', '""', $v) . '"', $row)) . "\n";
        }

        $filename = "laporan-donasi-{$streamer->slug}-{$dateFrom}-{$dateTo}.csv";

        return response($csv, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Export PDF
     */
    public function exportPdf(Request $request)
    {
        $streamer = Auth::user()->streamer;
        abort_unless($streamer, 403);

        $dateFrom = $request->input('from', now()->startOfMonth()->toDateString());
        $dateTo   = $request->input('to',   now()->toDateString());

        $donations = $streamer->donations()
            ->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->orderBy('created_at', 'desc')
            ->get();

        $totalAmount  = $donations->sum('amount');
        $totalCount   = $donations->count();
        $uniqueDonors = $donations->pluck('name')->unique()->count();

        try {
            $pdf = Pdf::loadView('reports.pdf', compact(
                'streamer', 'donations', 'dateFrom', 'dateTo',
                'totalAmount', 'totalCount', 'uniqueDonors'
            ))->setPaper('a4', 'landscape');

            $filename = "laporan-donasi-{$streamer->slug}-{$dateFrom}-{$dateTo}.pdf";
            return $pdf->download($filename);
        } catch (\Throwable $e) {
            Log::error('PDF export failed', [
                'streamer_id' => $streamer->id,
                'error'       => $e->getMessage(),
            ]);

            return redirect()->route('streamer.reports')
                ->with('error', 'Gagal mengekspor PDF. Silakan coba lagi atau gunakan ekspor CSV.');
        }
    }
}
