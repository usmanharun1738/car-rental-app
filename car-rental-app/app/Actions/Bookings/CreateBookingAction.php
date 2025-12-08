<?php

namespace App\Actions\Bookings;

use App\Enums\BookingStatus;
use App\Enums\VehicleStatus;
use App\Models\Booking;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CreateBookingAction
{
    public function __construct(
        protected CheckVehicleAvailabilityAction $checkAvailability,
        protected CalculateBookingPriceAction $calculatePrice
    ) {}

    /**
     * Create a new booking with concurrency protection.
     *
     * This method uses database transactions with row-level locking to prevent
     * double-bookings when multiple users try to book the same vehicle simultaneously.
     *
     * @param int $userId The customer's user ID
     * @param int $vehicleId The vehicle to book
     * @param Carbon $startTime Booking start time
     * @param Carbon $endTime Booking end time
     * @param string|null $notes Optional notes
     * @return Booking The created booking
     * @throws \Exception If the vehicle is not available
     */
    public function execute(
        int $userId,
        int $vehicleId,
        Carbon $startTime,
        Carbon $endTime,
        ?string $notes = null
    ): Booking {
        return DB::transaction(function () use ($userId, $vehicleId, $startTime, $endTime, $notes) {
            // 1. Lock the vehicle row to prevent concurrent modifications
            // This ensures no other transaction can read/write this row until we're done
            $vehicle = Vehicle::lockForUpdate()->findOrFail($vehicleId);

            // 2. Re-check availability INSIDE the lock (critical for concurrency safety)
            if (!$this->checkAvailability->execute($vehicle, $startTime, $endTime)) {
                throw new \Exception(
                    "Sorry, this vehicle is no longer available for the selected dates. " .
                    "Please choose different dates or another vehicle."
                );
            }

            // 3. Calculate the total price
            $totalPrice = $this->calculatePrice->execute($vehicle, $startTime, $endTime);

            // 4. Create the booking
            $booking = Booking::create([
                'user_id' => $userId,
                'vehicle_id' => $vehicleId,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'total_price' => $totalPrice,
                'status' => BookingStatus::PENDING,
                'notes' => $notes,
            ]);

            // 5. Optionally update vehicle status (if booking is confirmed immediately)
            // For now, we keep it as AVAILABLE until the booking is confirmed
            // $vehicle->update(['status' => VehicleStatus::BOOKED]);

            return $booking;
        });
    }
}
