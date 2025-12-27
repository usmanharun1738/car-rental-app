<?php

namespace App\Filament\Widgets;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Enums\VehicleStatus;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Review;
use App\Models\Vehicle;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // Calculate monthly revenue (payments with PAID status)
        $currentMonthRevenue = Payment::where('status', PaymentStatus::PAID)
            ->whereMonth('updated_at', Carbon::now()->month)
            ->whereYear('updated_at', Carbon::now()->year)
            ->sum('amount');

        $lastMonthRevenue = Payment::where('status', PaymentStatus::PAID)
            ->whereMonth('updated_at', Carbon::now()->subMonth()->month)
            ->whereYear('updated_at', Carbon::now()->subMonth()->year)
            ->sum('amount');

        $revenueChange = $lastMonthRevenue > 0 
            ? round((($currentMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1)
            : ($currentMonthRevenue > 0 ? 100 : 0);

        // Get last 7 days revenue for chart
        $revenueChart = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $revenueChart[] = Payment::where('status', PaymentStatus::PAID)
                ->whereDate('updated_at', $date)
                ->sum('amount') / 1000; // Divide by 1000 for better chart scale
        }

        // Active bookings
        $activeBookings = Booking::whereIn('status', [
            BookingStatus::CONFIRMED,
            BookingStatus::ACTIVE,
        ])->count();

        // Fleet stats
        $totalVehicles = Vehicle::count();
        $availableVehicles = Vehicle::where('status', VehicleStatus::AVAILABLE)->count();

        // Pending reviews
        $pendingReviews = Review::where('is_approved', false)->count();

        return [
            Stat::make('Monthly Revenue', '₦' . number_format($currentMonthRevenue))
                ->description($revenueChange >= 0 ? "+{$revenueChange}% from last month" : "{$revenueChange}% from last month")
                ->descriptionIcon($revenueChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->chart($revenueChart)
                ->color($revenueChange >= 0 ? 'success' : 'danger'),

            Stat::make('Active Bookings', $activeBookings)
                ->description('Confirmed & in progress')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('info'),

            Stat::make('Fleet Size', $totalVehicles)
                ->description("{$availableVehicles} available")
                ->descriptionIcon('heroicon-m-truck')
                ->color('primary'),

            Stat::make('Pending Reviews', $pendingReviews)
                ->description('Awaiting approval')
                ->descriptionIcon('heroicon-m-star')
                ->color($pendingReviews > 0 ? 'warning' : 'success'),
        ];
    }
}
