<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Attributes that should be mass assignable.
     * SECURITY: 'role' and 'is_active' removed to prevent privilege escalation.
     * These should only be set explicitly by admin/system code.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * Attributes that aren't mass assignable.
     */
    protected $guarded = [
        'id',
        'role',
        'is_active',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isStreamer(): bool
    {
        return $this->role === 'streamer';
    }

    public function streamer(): HasOne
    {
        return $this->hasOne(Streamer::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    /**
     * Relationship to OTP codes.
     */
    public function otpCodes(): HasMany
    {
        return $this->hasMany(OtpCode::class, 'email', 'email');
    }
}
