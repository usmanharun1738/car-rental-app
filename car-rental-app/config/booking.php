<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Buffer Time (in minutes)
    |--------------------------------------------------------------------------
    |
    | The minimum time gap required between bookings for the same vehicle.
    | This allows time for cleaning, inspection, and preparation.
    |
    */
    'buffer_minutes' => env('BOOKING_BUFFER_MINUTES', 60),

    /*
    |--------------------------------------------------------------------------
    | Minimum Booking Duration (in hours)
    |--------------------------------------------------------------------------
    |
    | The minimum duration for any booking.
    |
    */
    'min_duration_hours' => env('BOOKING_MIN_HOURS', 24),
];
