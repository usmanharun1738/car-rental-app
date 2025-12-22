<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;
use App\Http\Controllers\PaymentCallbackController;
use App\Http\Controllers\PaystackWebhookController;

Volt::route('/', 'pages.home')->name('home');

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
});

// Customer Dashboard
Volt::route('dashboard', 'pages.dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

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
