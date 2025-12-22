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
            ->with(['vehicle.images'])
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

    #[Computed]
    public function activeBooking()
    {
        return $this->bookings->whereIn('status', [BookingStatus::CONFIRMED, BookingStatus::ACTIVE])->first();
    }

    public function cancelBooking(int $bookingId): void
    {
        $booking = Booking::where('user_id', auth()->id())->find($bookingId);
        
        if ($booking && $booking->status === BookingStatus::PENDING) {
            $booking->update(['status' => BookingStatus::CANCELLED]);
            session()->flash('success', 'Booking cancelled successfully.');
        }
    }

    public function getStatusBadge(BookingStatus $status): array
    {
        return match($status) {
            BookingStatus::PENDING => ['bg' => 'bg-amber-100', 'text' => 'text-amber-700', 'icon' => 'schedule'],
            BookingStatus::CONFIRMED => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'icon' => 'verified'],
            BookingStatus::ACTIVE => ['bg' => 'bg-green-100', 'text' => 'text-green-700', 'icon' => 'directions_car'],
            BookingStatus::COMPLETED => ['bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'icon' => 'check_circle'],
            BookingStatus::CANCELLED => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'icon' => 'cancel'],
        };
    }
}; ?>

<div class="min-h-[70vh] bg-[#f6f7f8]">
    <!-- Header -->
    <div class="bg-gradient-to-r from-[#1E3A5F] to-[#2d5a8a] text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-black tracking-tight">Welcome back, {{ auth()->user()->name }}!</h1>
                    <p class="text-blue-200 mt-2">Manage your vehicle rentals and track your bookings</p>
                </div>
                <a href="{{ route('vehicles.index') }}" 
                   class="inline-flex items-center gap-2 px-6 py-3 bg-white/10 backdrop-blur border border-white/20 text-white font-semibold rounded-xl hover:bg-white/20 transition"
                   wire:navigate>
                    <span class="material-symbols-outlined">add</span>
                    New Booking
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Stats Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 -mt-14 mb-8 relative z-10">
            <div class="bg-white rounded-xl shadow-md p-5 border border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-[#1E3A5F]/10 flex items-center justify-center">
                        <span class="material-symbols-outlined text-[#1E3A5F]">calendar_month</span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Total</p>
                        <p class="text-2xl font-black text-gray-900">{{ $this->stats['total'] }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-md p-5 border border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center">
                        <span class="material-symbols-outlined text-amber-600">schedule</span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Pending</p>
                        <p class="text-2xl font-black text-amber-600">{{ $this->stats['pending'] }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-md p-5 border border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center">
                        <span class="material-symbols-outlined text-blue-600">verified</span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Confirmed</p>
                        <p class="text-2xl font-black text-blue-600">{{ $this->stats['confirmed'] }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-md p-5 border border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center">
                        <span class="material-symbols-outlined text-green-600">check_circle</span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Completed</p>
                        <p class="text-2xl font-black text-green-600">{{ $this->stats['completed'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 flex items-center gap-3">
                <span class="material-symbols-outlined">check_circle</span>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 flex items-center gap-3">
                <span class="material-symbols-outlined">error</span>
                {{ session('error') }}
            </div>
        @endif

        @if(session('info'))
            <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-xl text-blue-700 flex items-center gap-3">
                <span class="material-symbols-outlined">info</span>
                {{ session('info') }}
            </div>
        @endif

        <!-- Active Booking Highlight -->
        @if($this->activeBooking)
            <div class="mb-8 bg-gradient-to-r from-[#9CBF9B]/20 to-[#CFD186]/20 rounded-2xl p-6 border border-[#9CBF9B]/30">
                <div class="flex items-center gap-2 text-[#1E3A5F] mb-4">
                    <span class="material-symbols-outlined">directions_car</span>
                    <h2 class="font-bold text-lg">Your Active Rental</h2>
                </div>
                <div class="flex flex-col md:flex-row gap-6">
                    <div class="w-full md:w-48 h-32 rounded-xl overflow-hidden bg-white shadow-sm">
                        @if($this->activeBooking->vehicle?->primary_image_url)
                            <img src="{{ $this->activeBooking->vehicle->primary_image_url }}" 
                                 alt="{{ $this->activeBooking->vehicle->make }}"
                                 class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gray-100">
                                <span class="material-symbols-outlined text-gray-400 text-4xl">directions_car</span>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1">
                        <h3 class="text-xl font-bold text-gray-900">
                            {{ $this->activeBooking->vehicle?->make }} {{ $this->activeBooking->vehicle?->model }}
                        </h3>
                        <p class="text-gray-600 mt-1">
                            {{ $this->activeBooking->start_time->format('M d') }} - {{ $this->activeBooking->end_time->format('M d, Y') }}
                        </p>
                        <div class="flex items-center gap-4 mt-3">
                            @php $badge = $this->getStatusBadge($this->activeBooking->status); @endphp
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-sm font-semibold {{ $badge['bg'] }} {{ $badge['text'] }}">
                                <span class="material-symbols-outlined text-[16px]">{{ $badge['icon'] }}</span>
                                {{ $this->activeBooking->status->label() }}
                            </span>
                            <a href="{{ route('booking.success', $this->activeBooking) }}" 
                               class="text-[#E3655B] font-semibold text-sm hover:underline flex items-center gap-1">
                                View Details
                                <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Bookings List -->
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-900">All Bookings</h2>
            <span class="text-sm text-gray-500">{{ $this->bookings->count() }} {{ Str::plural('booking', $this->bookings->count()) }}</span>
        </div>

        @if($this->bookings->isEmpty())
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
                <div class="w-20 h-20 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-6">
                    <span class="material-symbols-outlined text-gray-400 text-4xl">calendar_month</span>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">No Bookings Yet</h3>
                <p class="text-gray-500 mb-8 max-w-sm mx-auto">You haven't made any bookings yet. Explore our fleet and find your perfect car today!</p>
                <a href="{{ route('vehicles.index') }}" 
                   class="inline-flex items-center gap-2 px-8 py-4 bg-[#E3655B] text-white font-bold rounded-xl hover:bg-[#d55549] transition shadow-lg shadow-[#E3655B]/20"
                   wire:navigate>
                    <span class="material-symbols-outlined">directions_car</span>
                    Browse Vehicles
                </a>
            </div>
        @else
            <div class="space-y-4">
                @foreach($this->bookings as $booking)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md hover:border-gray-200 transition-all">
                        <div class="flex flex-col md:flex-row">
                            <!-- Vehicle Image -->
                            <div class="w-full md:w-56 h-40 md:h-auto bg-gray-100 flex-shrink-0 relative">
                                @if($booking->vehicle?->primary_image_url)
                                    <img src="{{ $booking->vehicle->primary_image_url }}" 
                                         alt="{{ $booking->vehicle->make }}"
                                         class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <span class="material-symbols-outlined text-gray-400 text-4xl">directions_car</span>
                                    </div>
                                @endif
                                @php $badge = $this->getStatusBadge($booking->status); @endphp
                                <div class="absolute top-3 left-3">
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold {{ $badge['bg'] }} {{ $badge['text'] }} shadow-sm">
                                        <span class="material-symbols-outlined text-[14px]">{{ $badge['icon'] }}</span>
                                        {{ $booking->status->label() }}
                                    </span>
                                </div>
                            </div>

                            <!-- Booking Info -->
                            <div class="flex-1 p-5 md:p-6">
                                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                                    <div class="flex-1">
                                        <div class="flex items-start justify-between">
                                            <div>
                                                <h3 class="text-lg font-bold text-gray-900">
                                                    {{ $booking->vehicle?->make }} {{ $booking->vehicle?->model }}
                                                </h3>
                                                <p class="text-sm text-gray-500">{{ $booking->vehicle?->year }} • {{ $booking->vehicle?->transmission?->shortLabel() ?? 'Auto' }}</p>
                                            </div>
                                            <p class="text-sm text-gray-400 font-medium">#{{ str_pad($booking->id, 4, '0', STR_PAD_LEFT) }}</p>
                                        </div>
                                        
                                        <div class="flex flex-wrap gap-6 mt-4">
                                            <div class="flex items-center gap-2">
                                                <span class="material-symbols-outlined text-gray-400 text-[20px]">calendar_today</span>
                                                <div>
                                                    <p class="text-xs text-gray-500">Pick-up</p>
                                                    <p class="font-semibold text-sm">{{ $booking->start_time->format('M d, Y') }}</p>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <span class="material-symbols-outlined text-gray-400 text-[20px]">event</span>
                                                <div>
                                                    <p class="text-xs text-gray-500">Return</p>
                                                    <p class="font-semibold text-sm">{{ $booking->end_time->format('M d, Y') }}</p>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <span class="material-symbols-outlined text-gray-400 text-[20px]">location_on</span>
                                                <div>
                                                    <p class="text-xs text-gray-500">Location</p>
                                                    <p class="font-semibold text-sm">{{ $booking->vehicle?->location ?? 'Lagos' }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex flex-col items-end gap-3">
                                        <div class="text-right">
                                            <p class="text-xs text-gray-500">Total</p>
                                            <p class="text-2xl font-black text-[#1E3A5F]">₦{{ number_format($booking->total_price) }}</p>
                                        </div>
                                        
                                        <div class="flex gap-2">
                                            @if($booking->status === BookingStatus::PENDING)
                                                <a href="{{ route('booking.payment', $booking) }}"
                                                   class="inline-flex items-center gap-1 px-5 py-2.5 bg-[#E3655B] text-white text-sm font-bold rounded-lg hover:bg-[#d55549] transition shadow-sm">
                                                    <span class="material-symbols-outlined text-[18px]">credit_card</span>
                                                    Pay Now
                                                </a>
                                                <button wire:click="cancelBooking({{ $booking->id }})"
                                                        wire:confirm="Are you sure you want to cancel this booking?"
                                                        class="inline-flex items-center gap-1 px-4 py-2.5 border border-gray-200 text-gray-600 text-sm font-semibold rounded-lg hover:bg-gray-50 transition">
                                                    Cancel
                                                </button>
                                            @elseif($booking->status === BookingStatus::CONFIRMED || $booking->status === BookingStatus::COMPLETED)
                                                <a href="{{ route('booking.success', $booking) }}"
                                                   class="inline-flex items-center gap-1 px-5 py-2.5 bg-[#1E3A5F] text-white text-sm font-bold rounded-lg hover:bg-[#152a45] transition">
                                                    <span class="material-symbols-outlined text-[18px]">visibility</span>
                                                    View Details
                                                </a>
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
        <div class="mt-10 flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('vehicles.index') }}" 
               class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-[#E3655B] text-white font-bold rounded-xl hover:bg-[#d55549] transition shadow-lg shadow-[#E3655B]/20"
               wire:navigate>
                <span class="material-symbols-outlined">directions_car</span>
                Browse Vehicles
            </a>
            <a href="{{ route('home') }}" 
               class="inline-flex items-center justify-center gap-2 px-6 py-3 border border-gray-200 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition"
               wire:navigate>
                <span class="material-symbols-outlined">home</span>
                Back to Home
            </a>
        </div>
    </div>
</div>
