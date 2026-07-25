<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Donation extends Model
{
    use HasFactory;

    protected $fillable = [
        'streamer_id',
        'milestone_id',
        'name',
        'amount',
        'emoji',
        'message',
        'yt_url',
        'media_path',
        'ip_address',
        'status',
        'payment_reference',
        'payment_type',
        'paid_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     * IP addresses should never be exposed in JSON responses.
     */
    protected $hidden = [
        'ip_address',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'paid_at' => 'datetime',
        ];
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function streamer(): BelongsTo
    {
        return $this->belongsTo(Streamer::class);
    }

    public function milestone(): BelongsTo
    {
        return $this->belongsTo(Milestone::class);
    }

    public function alertQueue(): HasMany
    {
        return $this->hasMany(AlertQueue::class);
    }

    /**
     * Format amount ke Rupiah
     */
    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }
}
