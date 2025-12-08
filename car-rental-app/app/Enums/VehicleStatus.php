<?php

namespace App\Enums;

enum VehicleStatus: string
{
    case AVAILABLE = 'available';
    case BOOKED = 'booked';
    case MAINTENANCE = 'maintenance';

    public function label(): string
    {
        return match($this) {
            self::AVAILABLE => 'Available',
            self::BOOKED => 'Booked',
            self::MAINTENANCE => 'Under Maintenance',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::AVAILABLE => 'success',
            self::BOOKED => 'warning',
            self::MAINTENANCE => 'danger',
        };
    }
}
