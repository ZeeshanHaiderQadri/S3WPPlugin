<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MediaUpload extends Model
{
    protected $fillable = [
        'license_id',
        'license_activation_id',
        'domain',
        'attachment_id',
        'file_name',
        'file_type',
        'file_size',
        's3_key',
        's3_url',
        'uploaded_at',
    ];

    protected $casts = [
        'attachment_id' => 'integer',
        'file_size' => 'integer',
        'uploaded_at' => 'datetime',
    ];

    public function license(): BelongsTo
    {
        return $this->belongsTo(License::class);
    }

    public function activation(): BelongsTo
    {
        return $this->belongsTo(LicenseActivation::class, 'license_activation_id');
    }

    public function getFormattedFileSizeAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
