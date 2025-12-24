<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class License extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'plan_id',
        'license_key',
        'status',
        'max_activations',
        'activated_at',
        'expires_at',
        'last_checked_at',
        'metadata',
    ];

    protected $casts = [
        'max_activations' => 'integer',
        'activated_at' => 'datetime',
        'expires_at' => 'datetime',
        'last_checked_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function activations(): HasMany
    {
        return $this->hasMany(LicenseActivation::class);
    }

    public function mediaUploads(): HasMany
    {
        return $this->hasMany(MediaUpload::class);
    }

    public function usageStats(): HasMany
    {
        return $this->hasMany(UsageStat::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && 
               ($this->expires_at === null || $this->expires_at->isFuture());
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function canActivate(): bool
    {
        return $this->isActive() && 
               $this->activations()->where('is_active', true)->count() < $this->max_activations;
    }

    public function getActiveActivationsCount(): int
    {
        return $this->activations()->where('is_active', true)->count();
    }

    public function getTotalMediaUploads(): int
    {
        return $this->mediaUploads()->count();
    }

    public function getRemainingMediaLimit(): ?int
    {
        if ($this->plan->isUnlimited()) {
            return null;
        }

        $used = $this->getTotalMediaUploads();
        return max(0, $this->plan->media_limit - $used);
    }

    public function hasReachedMediaLimit(): bool
    {
        if ($this->plan->isUnlimited()) {
            return false;
        }

        return $this->getTotalMediaUploads() >= $this->plan->media_limit;
    }

    public function getUsagePercentage(): float
    {
        if ($this->plan->isUnlimited()) {
            return 0;
        }

        $used = $this->getTotalMediaUploads();
        return ($used / $this->plan->media_limit) * 100;
    }
}
