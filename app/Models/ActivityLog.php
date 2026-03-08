<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'streamer_id',
        'action',
        'description',
        'ip_address',
        'user_agent',
        'payload',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function streamer(): BelongsTo
    {
        return $this->belongsTo(Streamer::class);
    }

    /**
     * Log activity helper
     */
    public static function log(
        string $action,
        ?string $description = null,
        ?int $userId = null,
        ?int $streamerId = null,
        ?array $payload = null
    ): self {
        return static::create([
            'user_id' => $userId,
            'streamer_id' => $streamerId,
            'action' => $action,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'payload' => $payload,
            'created_at' => now(),
        ]);
    }
}
