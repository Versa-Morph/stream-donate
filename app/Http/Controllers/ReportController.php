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
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    /**
     * Sanitize a value for CSV export to prevent CSV injection attacks.
     * Prefixes potentially dangerous characters with a single quote.
     */
    private function sanitizeCsvValue(mixed $value): string
    {
        $value = (string) $value;
        
        // Characters that could trigger formula execution in spreadsheet apps
        $dangerousChars = ['=', '+', '-', '@', "\t", "\r", "\n"];
        
        // If the value starts with a dangerous character, prefix with single quote
        if ($value !== '' && in_array($value[0], $dangerousChars, true)) {
            return "'" . $value;
        }
        
        return $value;
    }
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
     * Export CSV with streaming for memory efficiency
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $streamer = Auth::user()->streamer;
        abort_unless($streamer, 403);

        $dateFrom = $request->input('from', now()->startOfMonth()->toDateString());
        $dateTo   = $request->input('to',   now()->toDateString());

        $filename = "laporan-donasi-{$streamer->slug}-{$dateFrom}-{$dateTo}.csv";

        return new StreamedResponse(function () use ($streamer, $dateFrom, $dateTo) {
            $handle = fopen('php://output', 'w');
            
            // Add BOM for proper UTF-8 encoding in Excel
            fwrite($handle, "\xEF\xBB\xBF");
            
            // Header row
            fputcsv($handle, ['No', 'Tanggal', 'Nama', 'Nominal (Rp)', 'Emoji', 'Pesan', 'YouTube URL']);
            
            // Stream data in chunks to prevent memory issues
            $counter = 0;
            $streamer->donations()
                ->whereDate('created_at', '>=', $dateFrom)
                ->whereDate('created_at', '<=', $dateTo)
                ->orderBy('created_at', 'desc')
                ->chunk(500, function ($donations) use ($handle, &$counter) {
                    foreach ($donations as $d) {
                        $counter++;
                        fputcsv($handle, [
                            $counter,
                            $d->created_at->format('d/m/Y H:i'),
                            $this->sanitizeCsvValue($d->name),
                            $d->amount,
                            $this->sanitizeCsvValue($d->emoji),
                            $this->sanitizeCsvValue($d->message ?? ''),
                            $this->sanitizeCsvValue($d->yt_url ?? ''),
                        ]);
                    }
                });
            
            fclose($handle);
        }, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control'       => 'no-cache, no-store, must-revalidate',
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
