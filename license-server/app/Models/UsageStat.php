<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UsageStat extends Model
{
    protected $fillable = [
        'license_id',
        'date',
        'uploads_count',
        'total_size',
        'api_calls',
    ];

    protected $casts = [
        'date' => 'date',
        'uploads_count' => 'integer',
        'total_size' => 'integer',
        'api_calls' => 'integer',
    ];

    public function license(): BelongsTo
    {
        return $this->belongsTo(License::class);
    }

    public function incrementUploads(int $fileSize = 0): void
    {
        $this->increment('uploads_count');
        $this->increment('total_size', $fileSize);
    }

    public function incrementApiCalls(): void
    {
        $this->increment('api_calls');
    }

    public function getFormattedTotalSizeAttribute(): string
    {
        $bytes = $this->total_size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
