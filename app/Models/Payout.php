<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payout extends Model
{
    use HasFactory;

    protected $fillable = [
        'streamer_id',
        'gross_amount',
        'platform_fee_amount',
        'net_amount',
        'status',
        'bank_name',
        'bank_account_number',
        'bank_account_holder',
        'reference',
        'notes',
        'created_by',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'gross_amount' => 'integer',
            'platform_fee_amount' => 'integer',
            'net_amount' => 'integer',
            'paid_at' => 'datetime',
        ];
    }

    public function streamer(): BelongsTo
    {
        return $this->belongsTo(Streamer::class);
    }

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getFormattedNetAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->net_amount, 0, ',', '.');
    }
}
