<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Enums\VehicleStatus;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    protected $fillable = [
        'make',
        'model',
        'year',
        'plate_number',
        'daily_rate',
        'status',
        'image_url',
        'features',
    ];

    protected $casts = [
        'status' => VehicleStatus::class,
        'features' => 'array',
        'daily_rate' => 'decimal:2',
    ];

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function maintenanceLogs(): HasMany
    {
        return $this->hasMany(MaintenanceLog::class);
    }
}
