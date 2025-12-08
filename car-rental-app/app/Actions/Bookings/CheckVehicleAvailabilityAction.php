<?php

namespace App\Actions\Bookings;

use App\Enums\BookingStatus;
use App\Enums\VehicleStatus;
use App\Models\Booking;
use App\Models\Vehicle;
use Carbon\Carbon;

class CheckVehicleAvailabilityAction
{
    /**
     * Check if a vehicle is available for booking during the given date range.
     *
     * @param Vehicle $vehicle The vehicle to check
     * @param Carbon $startTime Desired booking start time
     * @param Carbon $endTime Desired booking end time
     * @param int|null $excludeBookingId Exclude this booking ID (useful for edits)
     * @return bool True if available, false if not
     */
    public function execute(
        Vehicle $vehicle,
        Carbon $startTime,
        Carbon $endTime,
        ?int $excludeBookingId = null
    ): bool {
        // 1. Check if vehicle is in a bookable state
        if ($vehicle->status === VehicleStatus::MAINTENANCE) {
            return false;
        }

        // 2. Get buffer time from config (default 60 minutes)
        $bufferMinutes = config('booking.buffer_minutes', 60);

        // 3. Expand the search window by buffer time
        $searchStart = $startTime->copy()->subMinutes($bufferMinutes);
        $searchEnd = $endTime->copy()->addMinutes($bufferMinutes);

        // 4. Check for overlapping bookings (excluding cancelled ones)
        $conflictingBooking = Booking::query()
            ->where('vehicle_id', $vehicle->id)
            ->whereNotIn('status', [BookingStatus::CANCELLED])
            ->when($excludeBookingId, fn ($query) => $query->where('id', '!=', $excludeBookingId))
            ->where(function ($query) use ($searchStart, $searchEnd) {
                // Overlap logic: existing booking overlaps if:
                // (existing.start < desired.end) AND (existing.end > desired.start)
                $query->where('start_time', '<', $searchEnd)
                      ->where('end_time', '>', $searchStart);
            })
            ->exists();

        return !$conflictingBooking;
    }
}
