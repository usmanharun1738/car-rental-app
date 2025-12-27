<?php

namespace App\Observers;

use App\Enums\BookingStatus;
use App\Enums\VehicleStatus;
use App\Models\Booking;

class BookingObserver
{
    /**
     * Handle the Booking "created" event.
     */
    public function created(Booking $booking): void
    {
        $this->syncVehicleStatus($booking);
    }

    /**
     * Handle the Booking "updated" event.
     */
    public function updated(Booking $booking): void
    {
        // Only sync if status changed
        if ($booking->isDirty('status')) {
            $this->syncVehicleStatus($booking);
        }
    }

    /**
     * Sync vehicle status based on booking status.
     */
    protected function syncVehicleStatus(Booking $booking): void
    {
        $vehicle = $booking->vehicle;
        
        if (!$vehicle) {
            return;
        }

        // Determine what status the vehicle should have
        $newStatus = match ($booking->status) {
            BookingStatus::CONFIRMED, BookingStatus::ACTIVE => VehicleStatus::BOOKED,
            BookingStatus::COMPLETED, BookingStatus::CANCELLED => $this->shouldBeAvailable($vehicle) 
                ? VehicleStatus::AVAILABLE 
                : $vehicle->status,
            default => $vehicle->status, // PENDING doesn't change vehicle status
        };

        // Only update if status actually changes
        if ($vehicle->status !== $newStatus) {
            $vehicle->update(['status' => $newStatus]);
        }
    }

    /**
     * Check if vehicle should return to available status.
     * Only if there are no other active/confirmed bookings.
     */
    protected function shouldBeAvailable($vehicle): bool
    {
        return !$vehicle->bookings()
            ->whereIn('status', [BookingStatus::CONFIRMED, BookingStatus::ACTIVE])
            ->exists();
    }
}
