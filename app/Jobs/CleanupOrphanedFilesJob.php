<?php

namespace App\Jobs;

use App\Models\Streamer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Cleanup orphaned avatar and sound files not referenced in database.
 *
 * This job runs periodically to remove files that were left behind when:
 * - Avatar/sound replacement failed midway
 * - Old files weren't deleted after successful upload
 * - Streamer records were deleted but files remained
 *
 * Scheduled to run daily via App\Console\Kernel.
 */
class CleanupOrphanedFilesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Number of retry attempts.
     */
    public int $tries = 3;

    /**
     * Execute the job.
     *
     * Scans avatar and sound directories, compares with database references,
     * and removes files that are no longer needed.
     */
    public function handle(): void
    {
        $deletedAvatars = $this->cleanupOrphanedAvatars();
        $deletedSounds = $this->cleanupOrphanedSounds();

        Log::info("CleanupOrphanedFilesJob: cleaned up {$deletedAvatars} avatars, {$deletedSounds} sounds");
    }

    /**
     * Cleanup orphaned avatar files.
     *
     * @return int Number of files deleted
     */
    private function cleanupOrphanedAvatars(): int
    {
        $disk = Storage::disk('public');
        $directory = 'avatars';

        if (!$disk->exists($directory)) {
            return 0;
        }

        // Get all avatar files in storage
        $files = $disk->files($directory);

        // Get all avatar paths currently referenced in database
        $referencedPaths = Streamer::whereNotNull('avatar')
            ->pluck('avatar')
            ->map(fn($path) => str_replace('storage/', '', $path))
            ->toArray();

        $deleted = 0;

        foreach ($files as $file) {
            // Skip if file is referenced in database
            if (in_array($file, $referencedPaths)) {
                continue;
            }

            // Skip recently created files (less than 1 hour old) to avoid race conditions
            $lastModified = $disk->lastModified($file);
            if ($lastModified > now()->subHour()->timestamp) {
                continue;
            }

            try {
                $disk->delete($file);
                $deleted++;
                Log::debug("CleanupOrphanedFilesJob: deleted orphaned avatar", ['file' => $file]);
            } catch (\Throwable $e) {
                Log::warning("CleanupOrphanedFilesJob: failed to delete avatar", [
                    'file'  => $file,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $deleted;
    }

    /**
     * Cleanup orphaned sound files.
     *
     * @return int Number of files deleted
     */
    private function cleanupOrphanedSounds(): int
    {
        $disk = Storage::disk('public');
        $directory = 'sounds';

        if (!$disk->exists($directory)) {
            return 0;
        }

        // Get all sound files in storage
        $files = $disk->files($directory);

        // Get all custom sound paths currently referenced in database
        // Custom sounds are stored with full path; preset sounds are just names like 'ding', 'coin'
        $referencedPaths = Streamer::whereNotNull('notification_sound')
            ->where('notification_sound', 'like', 'storage/%') // Only custom sounds have storage/ prefix
            ->pluck('notification_sound')
            ->map(fn($path) => str_replace('storage/', '', $path))
            ->toArray();

        $deleted = 0;

        foreach ($files as $file) {
            // Skip if file is referenced in database
            if (in_array($file, $referencedPaths)) {
                continue;
            }

            // Skip recently created files (less than 1 hour old) to avoid race conditions
            $lastModified = $disk->lastModified($file);
            if ($lastModified > now()->subHour()->timestamp) {
                continue;
            }

            try {
                $disk->delete($file);
                $deleted++;
                Log::debug("CleanupOrphanedFilesJob: deleted orphaned sound", ['file' => $file]);
            } catch (\Throwable $e) {
                Log::warning("CleanupOrphanedFilesJob: failed to delete sound", [
                    'file'  => $file,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $deleted;
    }

    /**
     * Handle job failure after all retries exhausted.
     *
     * @param \Throwable $exception The exception that caused the failure
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('CleanupOrphanedFilesJob: all retries exhausted, orphaned files not cleaned', [
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}
