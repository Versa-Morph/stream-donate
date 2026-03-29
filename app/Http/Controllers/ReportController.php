<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\Streamer;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
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
        $streamer = auth()->user()->streamer;
        abort_unless($streamer, 403);

        $dateFrom = $request->input('from', now()->startOfMonth()->toDateString());
        $dateTo   = $request->input('to',   now()->toDateString());

        $baseQuery = $streamer->donations()
            ->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo);

        // Use database aggregation for stats (memory efficient)
        $stats = (clone $baseQuery)->selectRaw(
            'SUM(amount) as total_amount,
             COUNT(*) as total_count,
             COUNT(DISTINCT name) as unique_donors'
        )->first();

        $totalAmount  = (int) ($stats->total_amount ?? 0);
        $totalCount   = (int) ($stats->total_count ?? 0);
        $uniqueDonors = (int) ($stats->unique_donors ?? 0);
        $avgAmount    = $totalCount > 0 ? intdiv($totalAmount, $totalCount) : 0;

        // Get max donation separately (optimized query)
        $maxDonation = (clone $baseQuery)->orderByDesc('amount')->first();

        // Paginated for the history table, preserving filter params
        $donationsPaginated = (clone $baseQuery)
            ->orderBy('created_at', 'desc')
            ->paginate(config('pagination.report_donations', 25))
            ->withQueryString();

        // For backward compatibility with view (use paginated data, not full collection)
        $donations = $donationsPaginated;

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
        $streamer = auth()->user()->streamer;
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
     * Export PDF.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function exportPdf(Request $request): Response|RedirectResponse
    {
        $streamer = auth()->user()->streamer;
        abort_unless($streamer, 403);

        $dateFrom = $request->input('from', now()->startOfMonth()->toDateString());
        $dateTo   = $request->input('to',   now()->toDateString());

        // Maximum records for PDF to prevent memory issues
        $maxPdfRecords = config('export.pdf_max_records', 1000);

        // Get total count first
        $totalRecords = $streamer->donations()
            ->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->count();

        // Apply limit for PDF generation
        $donations = $streamer->donations()
            ->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->orderBy('created_at', 'desc')
            ->limit($maxPdfRecords)
            ->get();

        // Use database aggregation for stats (include all records, not just limited)
        $stats = $streamer->donations()
            ->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->selectRaw(
                'SUM(amount) as total_amount,
                 COUNT(*) as total_count,
                 COUNT(DISTINCT name) as unique_donors'
            )->first();

        $totalAmount  = (int) ($stats->total_amount ?? 0);
        $totalCount   = (int) ($stats->total_count ?? 0);
        $uniqueDonors = (int) ($stats->unique_donors ?? 0);

        // Flag to show warning in PDF if records were truncated
        $recordsTruncated = $totalRecords > $maxPdfRecords;

        try {
            $pdf = Pdf::loadView('reports.pdf', compact(
                'streamer', 'donations', 'dateFrom', 'dateTo',
                'totalAmount', 'totalCount', 'uniqueDonors',
                'recordsTruncated', 'maxPdfRecords', 'totalRecords'
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
