<?php

namespace Database\Seeders;

use App\Enums\FuelType;
use App\Enums\TransmissionType;
use App\Enums\VehicleStatus;
use App\Models\Feature;
use App\Models\Vehicle;
use App\Models\VehicleImage;
use Illuminate\Database\Seeder;

class VehicleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Vehicle templates with different makes/models
        $vehicleTemplates = [
            ['make' => 'Toyota', 'model' => 'Camry', 'base_rate' => 45000],
            ['make' => 'Toyota', 'model' => 'Corolla', 'base_rate' => 35000],
            ['make' => 'Toyota', 'model' => 'RAV4', 'base_rate' => 55000],
            ['make' => 'Honda', 'model' => 'Accord', 'base_rate' => 48000],
            ['make' => 'Honda', 'model' => 'Civic', 'base_rate' => 38000],
            ['make' => 'Honda', 'model' => 'CR-V', 'base_rate' => 52000],
            ['make' => 'Mercedes-Benz', 'model' => 'C300', 'base_rate' => 85000],
            ['make' => 'Mercedes-Benz', 'model' => 'E350', 'base_rate' => 120000],
            ['make' => 'Mercedes-Benz', 'model' => 'GLE', 'base_rate' => 150000],
            ['make' => 'BMW', 'model' => '3 Series', 'base_rate' => 80000],
            ['make' => 'BMW', 'model' => '5 Series', 'base_rate' => 110000],
            ['make' => 'BMW', 'model' => 'X5', 'base_rate' => 140000],
            ['make' => 'Lexus', 'model' => 'ES 350', 'base_rate' => 75000],
            ['make' => 'Lexus', 'model' => 'RX 350', 'base_rate' => 95000],
            ['make' => 'Audi', 'model' => 'A4', 'base_rate' => 82000],
            ['make' => 'Audi', 'model' => 'Q5', 'base_rate' => 105000],
            ['make' => 'Ford', 'model' => 'Mustang', 'base_rate' => 90000],
            ['make' => 'Ford', 'model' => 'Explorer', 'base_rate' => 65000],
            ['make' => 'Range Rover', 'model' => 'Sport', 'base_rate' => 180000],
            ['make' => 'Range Rover', 'model' => 'Evoque', 'base_rate' => 130000],
            ['make' => 'Porsche', 'model' => '911', 'base_rate' => 250000],
            ['make' => 'Porsche', 'model' => 'Cayenne', 'base_rate' => 200000],
            ['make' => 'Nissan', 'model' => 'Altima', 'base_rate' => 40000],
            ['make' => 'Nissan', 'model' => 'Pathfinder', 'base_rate' => 58000],
            ['make' => 'Hyundai', 'model' => 'Sonata', 'base_rate' => 36000],
        ];

        $fuelTypes = FuelType::cases();
        $transmissions = TransmissionType::cases();
        $years = [2020, 2021, 2022, 2023, 2024];
        $seatOptions = [4, 5, 5, 5, 7, 7, 8]; // weighted towards 5 seats
        
        // Get available images
        $imageFiles = glob(public_path('vehicle-images/*.jpg'));
        $imageCount = count($imageFiles);
        
        // Get all features for random assignment
        $featureIds = Feature::pluck('id')->toArray();
        
        $vehicleCount = 0;
        $targetCount = 50;
        
        // Clear existing vehicles and related data
        VehicleImage::query()->delete();
        Vehicle::query()->delete();
        
        while ($vehicleCount < $targetCount) {
            foreach ($vehicleTemplates as $template) {
                if ($vehicleCount >= $targetCount) break;
                
                $year = $years[array_rand($years)];
                $fuelType = $fuelTypes[array_rand($fuelTypes)];
                $transmission = $transmissions[array_rand($transmissions)];
                $seats = $seatOptions[array_rand($seatOptions)];
                
                // Vary the rate slightly
                $rateVariation = rand(-5000, 10000);
                $dailyRate = $template['base_rate'] + $rateVariation;
                
                // Generate plate number
                $plateNumber = strtoupper(substr(md5(rand()), 0, 3)) . '-' . rand(100, 999) . '-' . strtoupper(chr(rand(65, 90)) . chr(rand(65, 90)));
                
                $vehicle = Vehicle::create([
                    'make' => $template['make'],
                    'model' => $template['model'],
                    'year' => $year,
                    'plate_number' => $plateNumber,
                    'daily_rate' => $dailyRate,
                    'status' => VehicleStatus::AVAILABLE,
                    'fuel_type' => $fuelType,
                    'transmission' => $transmission,
                    'seats' => $seats,
                    'mileage' => rand(0, 1) ? 0 : rand(100, 300), // 50% unlimited, 50% limited
                ]);
                
                // Assign random features (3-7 features per vehicle)
                if (!empty($featureIds)) {
                    $numFeatures = rand(3, min(7, count($featureIds)));
                    $randomFeatures = array_rand(array_flip($featureIds), $numFeatures);
                    $vehicle->features()->attach(is_array($randomFeatures) ? $randomFeatures : [$randomFeatures]);
                }
                
                // Assign 2 random images
                if ($imageCount > 0) {
                    $imageIndex1 = rand(0, $imageCount - 1);
                    $imageIndex2 = ($imageIndex1 + 1) % $imageCount;
                    
                    VehicleImage::create([
                        'vehicle_id' => $vehicle->id,
                        'path' => 'vehicle-images/' . basename($imageFiles[$imageIndex1]),
                        'alt_text' => "{$vehicle->make} {$vehicle->model} - Front",
                        'sort_order' => 0,
                        'is_primary' => true,
                    ]);
                    
                    VehicleImage::create([
                        'vehicle_id' => $vehicle->id,
                        'path' => 'vehicle-images/' . basename($imageFiles[$imageIndex2]),
                        'alt_text' => "{$vehicle->make} {$vehicle->model} - Side",
                        'sort_order' => 1,
                        'is_primary' => false,
                    ]);
                }
                
                $vehicleCount++;
            }
        }
        
        $this->command->info("Created {$vehicleCount} vehicles with images and features!");
    }
}
