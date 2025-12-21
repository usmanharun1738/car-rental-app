<?php

namespace App\Enums;

enum TransmissionType: string
{
    case AUTOMATIC = 'automatic';
    case MANUAL = 'manual';

    public function label(): string
    {
        return match($this) {
            self::AUTOMATIC => 'Automatic',
            self::MANUAL => 'Manual',
        };
    }

    public function shortLabel(): string
    {
        return match($this) {
            self::AUTOMATIC => 'Auto',
            self::MANUAL => 'Manual',
        };
    }
}
