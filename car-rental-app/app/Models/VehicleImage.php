<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleImage extends Model
{
    protected $fillable = [
        'vehicle_id',
        'path',
        'alt_text',
        'sort_order',
        'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Get the full URL for the image
     */
    public function getUrlAttribute(): string
    {
        // If path starts with 'http' it's already a full URL
        if (str_starts_with($this->path, 'http')) {
            return $this->path;
        }
        
        // For images in storage (uploaded via Filament)
        if (str_starts_with($this->path, 'vehicle-images/') && file_exists(storage_path('app/public/' . $this->path))) {
            return asset('storage/' . $this->path);
        }
        
        // For images directly in public folder (seeded images)
        return asset($this->path);
    }
}
