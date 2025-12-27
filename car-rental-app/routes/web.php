<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;
use App\Http\Controllers\PaymentCallbackController;
use App\Http\Controllers\PaystackWebhookController;

Volt::route('/', 'pages.home')->name('home');

// Legal Pages
Volt::route('/terms', 'pages.terms')->name('terms');
Volt::route('/privacy', 'pages.privacy')->name('privacy');

// Payment callback (no auth required - Paystack redirects here)
Route::get('/payment/callback', [PaymentCallbackController::class, 'handle'])->name('payment.callback');

// Paystack webhook (no auth, no CSRF - server-to-server)
Route::post('/webhook/paystack', [PaystackWebhookController::class, 'handle'])->name('webhook.paystack');

// Vehicle Listing
Volt::route('/vehicles', 'pages.vehicles.index')->name('vehicles.index');

// Vehicle Details
Volt::route('/vehicles/{vehicle}', 'pages.vehicles.show')->name('vehicles.show');

// Booking (requires auth)
Route::middleware(['auth'])->group(function () {
    Volt::route('/booking/create', 'pages.booking.create')->name('booking.create');
    Volt::route('/booking/{booking}/payment', 'pages.booking.payment')->name('booking.payment');
    Volt::route('/booking/{booking}/success', 'pages.booking.success')->name('booking.success');
    
    // Receipt PDF
    Route::get('/booking/{booking}/receipt', [\App\Http\Controllers\BookingReceiptController::class, 'download'])->name('booking.receipt.download');
    Route::get('/booking/{booking}/receipt/view', [\App\Http\Controllers\BookingReceiptController::class, 'stream'])->name('booking.receipt.view');
});

// Customer Dashboard
Route::middleware(['auth', 'verified'])->group(function () {
    Volt::route('dashboard', 'pages.dashboard')->name('dashboard');
    Volt::route('dashboard/bookings', 'pages.dashboard.bookings')->name('dashboard.bookings');
    Volt::route('dashboard/profile', 'pages.dashboard.profile')->name('dashboard.profile');
    Volt::route('dashboard/security', 'pages.dashboard.security')->name('dashboard.security');
    Volt::route('dashboard/license', 'pages.dashboard.license')->name('dashboard.license');
});

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('user-password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');

    Volt::route('settings/two-factor', 'settings.two-factor')
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');
});
