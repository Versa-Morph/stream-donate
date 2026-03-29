<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Milestone extends Model
{
    use HasFactory;

    /**
     * Maximum milestones per streamer.
     */
    public const MAX_PER_STREAMER = 6;

    /**
     * Maximum title length.
     */
    public const MAX_TITLE_LENGTH = 80;

    protected $fillable = [
        'streamer_id',
        'title',
        'target_amount',
        'current_amount',
        'is_active',
        'is_completed',
        'order',
        'color',
        'description',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'target_amount' => 'integer',
            'current_amount' => 'integer',
            'is_active' => 'boolean',
            'is_completed' => 'boolean',
            'order' => 'integer',
            'completed_at' => 'datetime',
        ];
    }

    /**
     * Get the streamer that owns the milestone.
     */
    public function streamer(): BelongsTo
    {
        return $this->belongsTo(Streamer::class);
    }

    /**
     * Get all donations for this milestone.
     */
    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }

    /**
     * Get progress percentage (0-100).
     */
    public function getProgressPercentageAttribute(): int
    {
        if ($this->target_amount <= 0) {
            return 0;
        }
        
        $percent = ($this->current_amount / $this->target_amount) * 100;
        return min(100, (int) round($percent));
    }

    /**
     * Add donation amount to milestone and check if completed.
     */
    public function addAmount(int $amount): void
    {
        $this->increment('current_amount', $amount);
        $this->refresh();

        // Check if milestone is now completed
        if (!$this->is_completed && $this->current_amount >= $this->target_amount) {
            $this->update([
                'is_completed' => true,
                'completed_at' => now(),
            ]);
        }
    }

    /**
     * Scope query untuk milestone aktif saja.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope query untuk milestone yang belum completed.
     */
    public function scopeNotCompleted($query)
    {
        return $query->where('is_completed', false);
    }

    /**
     * Check if streamer can add more milestones.
     */
    public static function canStreamerAddMore(int $streamerId): bool
    {
        return self::where('streamer_id', $streamerId)->count() < self::MAX_PER_STREAMER;
    }

    /**
     * Get remaining milestone slots for streamer.
     */
    public static function remainingSlots(int $streamerId): int
    {
        $current = self::where('streamer_id', $streamerId)->count();
        return max(0, self::MAX_PER_STREAMER - $current);
    }
}

