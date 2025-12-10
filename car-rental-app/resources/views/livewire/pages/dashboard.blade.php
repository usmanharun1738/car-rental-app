<?php

use App\Models\Booking;
use App\Enums\BookingStatus;
use Livewire\Volt\Component;
use Livewire\Attributes\{Layout, Title, Computed};
use Carbon\Carbon;

new #[Layout('components.layouts.guest')] #[Title('My Bookings - CARTAR')] class extends Component
{
    #[Computed]
    public function bookings()
    {
        return Booking::where('user_id', auth()->id())
            ->with('vehicle')
            ->latest()
            ->get();
    }

    #[Computed]
    public function stats()
    {
        $bookings = $this->bookings;
        
        return [
            'total' => $bookings->count(),
            'pending' => $bookings->where('status', BookingStatus::PENDING)->count(),
            'confirmed' => $bookings->where('status', BookingStatus::CONFIRMED)->count(),
            'completed' => $bookings->where('status', BookingStatus::COMPLETED)->count(),
        ];
    }

    public function cancelBooking(int $bookingId): void
    {
        $booking = Booking::where('user_id', auth()->id())->find($bookingId);
        
        if ($booking && $booking->status === BookingStatus::PENDING) {
            $booking->update(['status' => BookingStatus::CANCELLED]);
            session()->flash('success', 'Booking cancelled successfully.');
        }
    }

    public function getStatusColor(BookingStatus $status): string
    {
        return match($status) {
            BookingStatus::PENDING => 'bg-amber-100 text-amber-800',
            BookingStatus::CONFIRMED => 'bg-blue-100 text-blue-800',
            BookingStatus::ACTIVE => 'bg-green-100 text-green-800',
            BookingStatus::COMPLETED => 'bg-gray-100 text-gray-800',
            BookingStatus::CANCELLED => 'bg-red-100 text-red-800',
        };
    }
}; ?>

<div class="min-h-[70vh]">
    <!-- Header -->
    <div class="bg-[#1E3A5F] text-white py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold">My Bookings</h1>
            <p class="text-gray-300 mt-2">Manage your vehicle rentals</p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Stats Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-xl shadow-sm p-4">
                <p class="text-sm text-gray-500">Total</p>
                <p class="text-2xl font-bold text-gray-900">{{ $this->stats['total'] }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-4">
                <p class="text-sm text-gray-500">Pending</p>
                <p class="text-2xl font-bold text-amber-600">{{ $this->stats['pending'] }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-4">
                <p class="text-sm text-gray-500">Confirmed</p>
                <p class="text-2xl font-bold text-blue-600">{{ $this->stats['confirmed'] }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-4">
                <p class="text-sm text-gray-500">Completed</p>
                <p class="text-2xl font-bold text-gray-600">{{ $this->stats['completed'] }}</p>
            </div>
        </div>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700">
                {{ session('error') }}
            </div>
        @endif

        @if(session('info'))
            <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg text-blue-700">
                {{ session('info') }}
            </div>
        @endif

        <!-- Bookings List -->
        @if($this->bookings->isEmpty())
            <div class="bg-white rounded-2xl shadow-md p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">No Bookings Yet</h3>
                <p class="text-gray-500 mb-6">You haven't made any bookings. Find your perfect car today!</p>
                <a href="{{ route('home') }}" 
                   class="inline-flex items-center px-6 py-3 bg-[#FF6B35] text-white font-semibold rounded-lg hover:bg-[#e55a2b] transition"
                   wire:navigate>
                    Browse Vehicles
                </a>
            </div>
        @else
            <div class="space-y-4">
                @foreach($this->bookings as $booking)
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-md transition">
                        <div class="flex flex-col md:flex-row">
                            <!-- Vehicle Image -->
                            <div class="w-full md:w-48 h-32 md:h-auto bg-gray-200 flex-shrink-0">
                                @if($booking->vehicle->image_url)
                                    <img src="{{ Storage::url($booking->vehicle->image_url) }}" 
                                         alt="{{ $booking->vehicle->make }}"
                                         class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            <!-- Booking Info -->
                            <div class="flex-1 p-4 md:p-6">
                                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                                    <div>
                                        <div class="flex items-center gap-3 mb-2">
                                            <h3 class="text-lg font-semibold text-gray-900">
                                                {{ $booking->vehicle->make }} {{ $booking->vehicle->model }}
                                            </h3>
                                            <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $this->getStatusColor($booking->status) }}">
                                                {{ $booking->status->value }}
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-500 mb-3">Booking #{{ $booking->id }}</p>
                                        
                                        <div class="grid grid-cols-2 gap-4 text-sm">
                                            <div>
                                                <p class="text-gray-500">Pick-up</p>
                                                <p class="font-medium">{{ Carbon::parse($booking->start_time)->format('M d, Y') }}</p>
                                            </div>
                                            <div>
                                                <p class="text-gray-500">Return</p>
                                                <p class="font-medium">{{ Carbon::parse($booking->end_time)->format('M d, Y') }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-right">
                                        <p class="text-2xl font-bold text-[#1E3A5F]">₦{{ number_format($booking->total_price) }}</p>
                                        
                                        <div class="flex gap-2 mt-4 justify-end">
                                            @if($booking->status === BookingStatus::PENDING)
                                                <a href="{{ route('booking.payment', $booking) }}"
                                                   class="px-4 py-2 bg-[#FF6B35] text-white text-sm font-medium rounded-lg hover:bg-[#e55a2b] transition">
                                                    Make Payment
                                                </a>
                                                <button wire:click="cancelBooking({{ $booking->id }})"
                                                        wire:confirm="Are you sure you want to cancel this booking?"
                                                        class="px-4 py-2 border border-red-300 text-red-600 text-sm font-medium rounded-lg hover:bg-red-50 transition">
                                                    Cancel
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Quick Actions -->
        <div class="mt-8 flex justify-center">
            <a href="{{ route('home') }}" 
               class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition"
               wire:navigate>
                ← Back to Home
            </a>
        </div>
    </div>
</div>
