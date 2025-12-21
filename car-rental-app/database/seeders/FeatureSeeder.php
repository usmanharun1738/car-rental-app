<?php

namespace Database\Seeders;

use App\Models\Feature;
use Illuminate\Database\Seeder;

class FeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $features = [
            'Apple CarPlay & Android Auto',
            'Adaptive Cruise Control',
            'Heated Leather Seats',
            'Navigation System',
            '360° Parking Camera',
            'Premium Sound System',
            'Bluetooth Connectivity',
            'Backup Camera',
            'Keyless Entry',
            'Push Button Start',
            'Sunroof/Moonroof',
            'Blind Spot Monitoring',
            'Lane Departure Warning',
            'USB Charging Ports',
            'Air Conditioning',
            'Power Windows',
            'Central Locking',
            'ABS Brakes',
            'Airbags',
            'Cruise Control',
        ];

        foreach ($features as $feature) {
            Feature::firstOrCreate(['name' => $feature]);
        }
    }
}
