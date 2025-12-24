<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LicenseActivation extends Model
{
    protected $fillable = [
        'license_id',
        'domain',
        'site_url',
        'ip_address',
        'wordpress_version',
        'plugin_version',
        'activated_at',
        'last_checked_at',
        'deactivated_at',
        'is_active',
    ];

    protected $casts = [
        'activated_at' => 'datetime',
        'last_checked_at' => 'datetime',
        'deactivated_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function license(): BelongsTo
    {
        return $this->belongsTo(License::class);
    }

    public function mediaUploads(): HasMany
    {
        return $this->hasMany(MediaUpload::class);
    }

    public function deactivate(): void
    {
        $this->update([
            'is_active' => false,
            'deactivated_at' => now(),
        ]);
    }

    public function updateLastChecked(): void
    {
        $this->update(['last_checked_at' => now()]);
    }
}
