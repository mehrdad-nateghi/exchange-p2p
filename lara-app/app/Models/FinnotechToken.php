<?php

namespace App\Models;

use App\Enums\FinnotechTokenTypeEnum;
use App\Services\ThirdParty\FinnoTech\FinnotechTokenService;
use App\Traits\Global\Paginatable;
use App\Traits\Global\Ulid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinnotechToken extends Model
{
    use HasFactory,Ulid, Paginatable;

    protected $fillable = [
        'ulid',
        'token_type',
        'access_token',
        'refresh_token',
        'scopes',
        'national_id',
        'bank_code',
        'lifetime',
        'expires_at',
        'is_active',
        'refresh_count',
        'last_usage',
        'metadata'
    ];

    protected $casts = [
        'token_type' => FinnotechTokenTypeEnum::class,
        'scopes' => 'array',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
        'refresh_count' => 'integer',
        'last_usage' => 'array',
        'metadata' => 'array'
    ];

    /**
     * Check if token is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Check if token needs refresh (within 5 minutes of expiry)
     */
    public function needsRefresh(): bool
    {
        $a = $this->expires_at
            ->subMinutes(FinnotechTokenService::TOKEN_REFRESH_BUFFER_MINUTES)
            ->isPast();

        $b = $this->expires_at;
        return $this->expires_at
            ->subMinutes(FinnotechTokenService::TOKEN_REFRESH_BUFFER_MINUTES)
            ->isPast();
    }

    /**
     * Get remaining time until expiration
     */
    public function getRemainingTime(): string
    {
        if ($this->isExpired()) {
            return 'Expired';
        }

        $hours = now()->diffInHours($this->expires_at);
        return "{$hours} hours";
    }

    /**
     * Update last usage information
     */
    public function updateLastUsage(string $service = null): void
    {
        $this->last_usage = [
            'last_used_at' => now()->toDateTimeString(),
            'service' => $service ?? 'unknown'
        ];
        $this->save();
    }

    /**
     * Increment refresh count
     */
    public function incrementRefreshCount(): void
    {
        $this->increment('refresh_count');
    }

    /**
     * Deactivate token
     */
    public function deactivate(): void
    {
        $this->is_active = false;
        $this->save();
    }

    /**
     * Scope for active tokens
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for valid tokens (not expired)
     */
    public function scopeValid($query)
    {
        return $query->where('expires_at', '>', now())
            ->where('is_active', true);
    }

    /**
     * Scope for client credentials tokens
     */
    public function scopeClientCredentials($query)
    {
        return $query->where('token_type', FinnotechTokenTypeEnum::CLIENT_CREDENTIALS->value);
    }

    /**
     * Scope for authorization code tokens
     */
    public function scopeAuthorizationCode($query)
    {
        return $query->where('token_type', FinnotechTokenTypeEnum::AUTHORIZATION_CODE->value);
    }

}
