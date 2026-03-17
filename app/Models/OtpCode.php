<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtpCode extends Model
{
    protected $fillable = ['email', 'code', 'expires_at', 'used_at', 'attempt_count', 'locked_until'];

    protected $casts = [
        'expires_at'   => 'datetime',
        'used_at'      => 'datetime',
        'locked_until' => 'datetime',
    ];

    public function isValid(): bool
    {
        return ! is_null($this->expires_at) 
            && is_null($this->used_at) 
            && $this->expires_at->isFuture();
    }

    public function isLocked(): bool
    {
        return ! is_null($this->locked_until) && $this->locked_until->isFuture();
    }

    public function incrementAttempts(): void
    {
        $this->increment('attempt_count');
        
        // Lock after 3 failed attempts for 1 hour
        if ($this->attempt_count >= 3) {
            $this->update(['locked_until' => now()->addHour()]);
        }
    }
}
