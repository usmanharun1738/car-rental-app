<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;

Volt::route('/', 'pages.home')->name('home');

// Vehicle Details
Volt::route('/vehicles/{vehicle}', 'pages.vehicles.show')->name('vehicles.show');

// Booking (requires auth)
Route::middleware(['auth'])->group(function () {
    Volt::route('/booking/create', 'pages.booking.create')->name('booking.create');
    Volt::route('/booking/{booking}/payment', 'pages.booking.payment')->name('booking.payment');
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
