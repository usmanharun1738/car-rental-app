<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Enums\VehicleStatus;
use App\Enums\FuelType;
use App\Enums\TransmissionType;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
        'fuel_type',
        'transmission',
        'seats',
        'mileage',
    ];

    protected $casts = [
        'status' => VehicleStatus::class,
        'fuel_type' => FuelType::class,
        'transmission' => TransmissionType::class,
        'daily_rate' => 'decimal:2',
        'seats' => 'integer',
        'mileage' => 'integer',
    ];

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function maintenanceLogs(): HasMany
    {
        return $this->hasMany(MaintenanceLog::class);
    }

    public function features(): BelongsToMany
    {
        return $this->belongsToMany(Feature::class);
    }

    /**
     * Get mileage display string
     */
    public function getMileageDisplayAttribute(): string
    {
        if ($this->mileage === null || $this->mileage === 0) {
            return 'Unlimited';
        }
        return number_format($this->mileage) . ' km/day';
    }
}
