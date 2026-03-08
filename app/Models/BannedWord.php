<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

class BannedWord extends Model
{
    protected $fillable = [
        'word',
        'streamer_id',
        'created_by',
    ];

    protected static function boot(): void
    {
        parent::boot();

        // Flush cache whenever a banned word is added or removed so the
        // ProfanityFilter picks up the change on the next request.
        static::saved(function (self $model) {
            Cache::forget('banned_words:global');
            if ($model->streamer_id) {
                Cache::forget("banned_words:{$model->streamer_id}");
            }
        });

        static::deleted(function (self $model) {
            Cache::forget('banned_words:global');
            if ($model->streamer_id) {
                Cache::forget("banned_words:{$model->streamer_id}");
            }
        });
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function streamer(): BelongsTo
    {
        return $this->belongsTo(Streamer::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    /** Global words (admin-managed, applies to all streamers). */
    public function scopeGlobal($query)
    {
        return $query->whereNull('streamer_id');
    }

    /** Words specific to a given streamer. */
    public function scopeForStreamer($query, int $streamerId)
    {
        return $query->where('streamer_id', $streamerId);
    }
}
