<?php

namespace App\Actions\Bookings;

use App\Models\Vehicle;
use Carbon\Carbon;

class CalculateBookingPriceAction
{
    /**
     * Calculate the total booking price based on duration and daily rate.
     *
     * @param Vehicle $vehicle The vehicle being booked
     * @param Carbon $startTime Booking start time
     * @param Carbon $endTime Booking end time
     * @return float Total price
     */
    public function execute(Vehicle $vehicle, Carbon $startTime, Carbon $endTime): float
    {
        // Calculate number of days (minimum 1 day)
        $days = max(1, $startTime->diffInDays($endTime));

        // Base calculation: days × daily rate
        $basePrice = $vehicle->daily_rate * $days;

        // Future: Add seasonal multipliers, discounts, driver fees here
        // Example:
        // if ($startTime->month === 12) {
        //     $basePrice *= 1.2; // 20% holiday surcharge
        // }

        return round($basePrice, 2);
    }
}
