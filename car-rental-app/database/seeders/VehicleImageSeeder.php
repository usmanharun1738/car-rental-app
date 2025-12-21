<?php

namespace Database\Seeders;

use App\Models\Vehicle;
use App\Models\VehicleImage;
use Illuminate\Database\Seeder;

class VehicleImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all available images from public/vehicle-images
        $imageFiles = glob(public_path('vehicle-images/*.jpg'));
        
        if (empty($imageFiles)) {
            $this->command->warn('No images found in public/vehicle-images/');
            return;
        }

        // Get all vehicles
        $vehicles = Vehicle::all();
        
        if ($vehicles->isEmpty()) {
            $this->command->warn('No vehicles found to assign images to.');
            return;
        }

        // Group images by vehicle type (based on filename prefix)
        $imageGroups = [];
        foreach ($imageFiles as $imagePath) {
            $filename = basename($imagePath);
            // Extract vehicle identifier (e.g., "toyota-camry" from "toyota-camry-1.jpg")
            preg_match('/^(.+?)-\d+\.jpg$/', $filename, $matches);
            $vehicleKey = $matches[1] ?? 'generic';
            $imageGroups[$vehicleKey][] = 'vehicle-images/' . $filename;
        }

        // Assign images to vehicles
        $imageGroupKeys = array_keys($imageGroups);
        $groupCount = count($imageGroupKeys);

        foreach ($vehicles as $index => $vehicle) {
            // Clear existing images for this vehicle
            $vehicle->images()->delete();
            
            // Pick an image group based on vehicle index (cycling through available groups)
            $groupKey = $imageGroupKeys[$index % $groupCount];
            $images = $imageGroups[$groupKey];
            
            foreach ($images as $sortOrder => $imagePath) {
                VehicleImage::create([
                    'vehicle_id' => $vehicle->id,
                    'path' => $imagePath,
                    'alt_text' => "{$vehicle->make} {$vehicle->model} - View " . ($sortOrder + 1),
                    'sort_order' => $sortOrder,
                    'is_primary' => $sortOrder === 0,
                ]);
            }
            
            $this->command->info("Assigned " . count($images) . " images to {$vehicle->make} {$vehicle->model}");
        }
        
        $this->command->info("Vehicle images seeded successfully!");
    }
}
