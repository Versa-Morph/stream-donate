<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AlertQueue extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'streamer_id',
        'donation_id',
        'seq',
        'payload',
        'expires_at',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'expires_at' => 'datetime',
            'created_at' => 'datetime',
            'seq' => 'integer',
        ];
    }

    public function streamer(): BelongsTo
    {
        return $this->belongsTo(Streamer::class);
    }

    public function donation(): BelongsTo
    {
        return $this->belongsTo(Donation::class);
    }

    public function scopeNotExpired($query)
    {
        return $query->where('expires_at', '>', now());
    }

    public function scopeAfterSeq($query, int $seq)
    {
        return $query->where('seq', '>', $seq);
    }
}
