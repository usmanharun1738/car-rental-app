<?php

use App\Models\Booking;
use App\Enums\BookingStatus;
use Livewire\Volt\Component;
use Livewire\Attributes\{Layout, Title, Computed};

new #[Layout('components.layouts.guest')] #[Title('Booking Confirmed - CARTAR')] class extends Component
{
    public Booking $booking;

    public function mount(Booking $booking): void
    {
        // Ensure user owns this booking
        if ($booking->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this booking.');
        }

        $this->booking = $booking->load(['vehicle.images', 'payments']);
    }

    #[Computed]
    public function vehicle()
    {
        return $this->booking->vehicle;
    }

    #[Computed]
    public function totalDays(): int
    {
        return max(1, $this->booking->start_time->diffInDays($this->booking->end_time));
    }

    #[Computed]
    public function isPaid(): bool
    {
        return $this->booking->status === BookingStatus::CONFIRMED;
    }
}; ?>

<div class="min-h-[70vh] bg-[#f6f7f8]">
    <div class="max-w-7xl mx-auto px-4 md:px-8 lg:px-12 py-8">
        <!-- Success Header -->
        <div class="mb-8 flex flex-col md:flex-row md:items-start justify-between gap-6 bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <div class="flex gap-4">
                <div class="flex-shrink-0 mt-1">
                    <div class="size-12 rounded-full bg-[#9CBF9B]/20 flex items-center justify-center">
                        <span class="material-symbols-outlined text-[#9CBF9B]" style="font-size: 28px;">check_circle</span>
                    </div>
                </div>
                <div class="flex flex-col gap-2">
                    <h1 class="text-3xl font-black leading-tight tracking-[-0.033em]">Booking Confirmed</h1>
                    <p class="text-gray-500 text-base max-w-2xl">
                        Thank you, {{ auth()->user()->name }}! Your ride is reserved. A confirmation email has been sent to <span class="font-medium text-gray-900">{{ auth()->user()->email }}</span>.
                    </p>
                </div>
            </div>
            <div class="flex flex-col items-start md:items-end gap-2">
                <span class="text-sm text-gray-500 font-medium">Confirmation Number</span>
                <div class="flex items-center justify-center rounded-lg h-10 px-4 bg-gray-100 text-[#E3655B] text-base font-bold border border-gray-200">
                    #TRV-{{ str_pad($booking->id, 4, '0', STR_PAD_LEFT) }}
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
            <!-- Left Column -->
            <div class="lg:col-span-2 flex flex-col gap-6">
                <!-- Vehicle Details -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="p-6">
                        <h3 class="text-xl font-bold mb-4 flex items-center gap-2">
                            <span class="material-symbols-outlined text-[#9CBF9B]">directions_car</span>
                            Vehicle Details
                        </h3>
                        <div class="flex flex-col md:flex-row gap-6">
                            <div class="w-full md:w-1/2 aspect-video bg-gray-100 rounded-lg overflow-hidden">
                                @if($this->vehicle?->primary_image_url)
                                    <img src="{{ $this->vehicle->primary_image_url }}" 
                                         alt="{{ $this->vehicle->make }} {{ $this->vehicle->model }}"
                                         class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <span class="material-symbols-outlined text-gray-400 text-6xl">directions_car</span>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 flex flex-col justify-center">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <p class="text-lg font-bold">{{ $this->vehicle?->make }} {{ $this->vehicle?->model }}</p>
                                        <p class="text-gray-500 text-sm">or similar {{ $this->vehicle?->transmission?->label() ?? 'Auto' }} vehicle</p>
                                    </div>
                                    <span class="px-2 py-1 bg-[#CFD186]/40 text-gray-800 text-xs font-bold rounded uppercase tracking-wide">Premium</span>
                                </div>
                                <div class="grid grid-cols-2 gap-y-3 gap-x-2 mt-4">
                                    <div class="flex items-center gap-2 text-sm text-gray-600">
                                        <span class="material-symbols-outlined text-[18px]">airline_seat_recline_normal</span>
                                        {{ $this->vehicle?->seats ?? 5 }} Passengers
                                    </div>
                                    <div class="flex items-center gap-2 text-sm text-gray-600">
                                        <span class="material-symbols-outlined text-[18px]">local_gas_station</span>
                                        {{ $this->vehicle?->fuel_type?->label() ?? 'Gasoline' }}
                                    </div>
                                    <div class="flex items-center gap-2 text-sm text-gray-600">
                                        <span class="material-symbols-outlined text-[18px]">settings</span>
                                        {{ $this->vehicle?->transmission?->label() ?? 'Automatic' }}
                                    </div>
                                    <div class="flex items-center gap-2 text-sm text-gray-600">
                                        <span class="material-symbols-outlined text-[18px]">luggage</span>
                                        3 Bags
                                    </div>
                                    <div class="flex items-center gap-2 text-sm text-[#9CBF9B] col-span-2 mt-1 font-medium">
                                        <span class="material-symbols-outlined text-[18px]">check</span>
                                        {{ $this->vehicle?->mileage_display ?? 'Unlimited Mileage Included' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Itinerary -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-xl font-bold mb-6 flex items-center gap-2">
                        <span class="material-symbols-outlined text-[#9CBF9B]">calendar_month</span>
                        Itinerary
                    </h3>
                    <div class="flex gap-4">
                        <div class="flex flex-col items-center pt-1">
                            <div class="size-10 rounded-full bg-[#9CBF9B]/20 text-[#9CBF9B] flex items-center justify-center">
                                <span class="material-symbols-outlined text-[22px]">location_on</span>
                            </div>
                        </div>
                        <div class="pb-2">
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Location</p>
                            <p class="text-lg font-bold text-gray-900">{{ $this->vehicle?->location ?? 'Lagos' }}</p>
                            <p class="text-gray-600">{{ $booking->start_time->format('D, M d, h:i A') }} - {{ $booking->end_time->format('D, M d, h:i A') }}</p>
                        </div>
                    </div>
                </div>

                <!-- What to Bring -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-xl font-bold mb-4 flex items-center gap-2">
                        <span class="material-symbols-outlined text-[#9CBF9B]">inventory</span>
                        What to bring at pickup
                    </h3>
                    <div class="grid sm:grid-cols-3 gap-4">
                        <div class="flex flex-col gap-2 p-4 rounded-lg bg-gray-50 border border-transparent hover:border-[#9CBF9B]/30 transition-all">
                            <span class="material-symbols-outlined text-[#9CBF9B] text-3xl">badge</span>
                            <span class="font-bold text-sm">Driver's License</span>
                            <span class="text-xs text-gray-500">Valid and physical copy</span>
                        </div>
                        <div class="flex flex-col gap-2 p-4 rounded-lg bg-gray-50 border border-transparent hover:border-[#9CBF9B]/30 transition-all">
                            <span class="material-symbols-outlined text-[#9CBF9B] text-3xl">credit_card</span>
                            <span class="font-bold text-sm">Credit Card</span>
                            <span class="text-xs text-gray-500">Must match driver's name</span>
                        </div>
                        <div class="flex flex-col gap-2 p-4 rounded-lg bg-gray-50 border border-transparent hover:border-[#9CBF9B]/30 transition-all">
                            <span class="material-symbols-outlined text-[#9CBF9B] text-3xl">mail</span>
                            <span class="font-bold text-sm">Voucher</span>
                            <span class="text-xs text-gray-500">Digital or printed copy</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="flex flex-col gap-6">
                <!-- Payment Summary -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="bg-gray-50 p-4 border-b border-gray-200">
                        <h3 class="text-lg font-bold">Payment Summary</h3>
                    </div>
                    <div class="p-6 flex flex-col gap-3">
                        <div class="flex justify-between text-sm text-gray-600">
                            <span>Car Rental ({{ $this->totalDays }} {{ Str::plural('Day', $this->totalDays) }})</span>
                            <span>₦{{ number_format($booking->total_price) }}</span>
                        </div>
                        <div class="h-px bg-gray-200 my-2"></div>
                        <div class="flex justify-between items-center">
                            <span class="text-base font-bold text-gray-900">Total Paid</span>
                            <span class="text-2xl font-black text-[#E3655B]">₦{{ number_format($booking->total_price) }}</span>
                        </div>
                        <div class="mt-2 text-xs text-gray-400 text-right">NGN</div>
                    </div>
                    <div class="bg-[#9CBF9B]/10 p-3 text-center border-t border-[#9CBF9B]/20">
                        <p class="text-xs font-bold text-[#9CBF9B] flex items-center justify-center gap-1">
                            <span class="material-symbols-outlined text-[16px]">lock</span>
                            Payment Securely Processed
                        </p>
                    </div>
                </div>

                <!-- Need Help -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-start gap-4">
                        <div class="size-10 rounded-full bg-[#CFD186]/50 flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-outlined text-gray-800">support_agent</span>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-900 mb-1">Need help?</p>
                            <p class="text-xs text-gray-500 mb-3">Call our support team 24/7 regarding your booking.</p>
                            <a class="text-sm font-bold text-[#E3655B] hover:underline" href="tel:+2348001234567">+234 800 123 4567</a>
                        </div>
                    </div>
                </div>

                <!-- Print Button -->
                <button onclick="window.print()" class="w-full py-3 rounded-lg border border-gray-200 text-gray-600 font-bold text-sm hover:bg-gray-50 transition-colors flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined">print</span>
                    Print Confirmation
                </button>

                <!-- Download Receipt -->
                <a href="{{ route('booking.receipt.download', $booking) }}" 
                   class="w-full py-3 rounded-lg border border-[#E3655B] text-[#E3655B] font-bold text-sm hover:bg-[#E3655B]/5 transition-colors flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined">download</span>
                    Download Receipt PDF
                </a>

                <!-- Back to Dashboard -->
                <a href="{{ route('dashboard') }}" 
                   class="w-full py-3 rounded-lg bg-[#1E3A5F] text-white font-bold text-sm hover:bg-[#152a45] transition-colors flex items-center justify-center gap-2"
                   wire:navigate>
                    <span class="material-symbols-outlined">dashboard</span>
                    Go to Dashboard
                </a>
            </div>
        </div>
    </div>
</div>
