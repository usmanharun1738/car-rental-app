<?php

namespace Database\Seeders;

use App\Enums\VehicleStatus;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;

class VehicleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vehicles = [
            [
                'make' => 'Toyota',
                'model' => 'Camry',
                'year' => 2023,
                'plate_number' => 'ABC-123-LG',
                'daily_rate' => 25000,
                'status' => VehicleStatus::AVAILABLE,
                'features' => ['Air Conditioning', 'Bluetooth', 'Backup Camera'],
            ],
            [
                'make' => 'Mercedes Benz',
                'model' => 'C300',
                'year' => 2022,
                'plate_number' => 'XYZ-456-AB',
                'daily_rate' => 50000,
                'status' => VehicleStatus::AVAILABLE,
                'features' => ['Leather Seats', 'Sunroof', 'Navigation', 'Premium Sound'],
            ],
            [
                'make' => 'Honda',
                'model' => 'Accord',
                'year' => 2021,
                'plate_number' => 'DEF-789-KJ',
                'daily_rate' => 20000,
                'status' => VehicleStatus::AVAILABLE,
                'features' => ['Air Conditioning', 'Bluetooth'],
            ],
            [
                'make' => 'BMW',
                'model' => 'X5',
                'year' => 2023,
                'plate_number' => 'BMW-001-NG',
                'daily_rate' => 75000,
                'status' => VehicleStatus::AVAILABLE,
                'features' => ['Leather Seats', 'Panoramic Roof', 'Navigation', 'Apple CarPlay'],
            ],
            [
                'make' => 'Lexus',
                'model' => 'RX 350',
                'year' => 2022,
                'plate_number' => 'LEX-222-AB',
                'daily_rate' => 60000,
                'status' => VehicleStatus::AVAILABLE,
                'features' => ['Leather Seats', 'Mark Levinson Audio', 'Heated Seats'],
            ],
            [
                'make' => 'Toyota',
                'model' => 'Land Cruiser',
                'year' => 2023,
                'plate_number' => 'TLC-300-LG',
                'daily_rate' => 100000,
                'status' => VehicleStatus::AVAILABLE,
                'features' => ['4WD', 'Leather Seats', 'Sunroof', 'Premium Audio', 'GPS Navigation'],
            ],
        ];

        foreach ($vehicles as $vehicle) {
            Vehicle::create($vehicle);
        }
    }
}
