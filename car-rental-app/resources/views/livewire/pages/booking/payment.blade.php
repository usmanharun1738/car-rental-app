<?php

use App\Models\Booking;
use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Actions\Payments\ProcessPaymentAction;
use Livewire\Volt\Component;
use Livewire\Attributes\{Layout, Title, Computed};

new #[Layout('components.layouts.guest')] #[Title('Complete Payment - CARTAR')] class extends Component
{
    public Booking $booking;
    public bool $processing = false;
    public ?string $errorMessage = null;

    public function mount(Booking $booking): void
    {
        // Ensure user owns this booking
        if ($booking->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this booking.');
        }

        // Check if booking is in a payable state
        if ($booking->status !== BookingStatus::PENDING) {
            session()->flash('info', 'This booking is already ' . $booking->status->label() . '.');
            $this->redirect(route('dashboard'));
            return;
        }

        // Check if already paid
        $paidPayment = $booking->payments()
            ->where('status', PaymentStatus::PAID)
            ->first();
        
        if ($paidPayment) {
            session()->flash('info', 'This booking has already been paid.');
            $this->redirect(route('dashboard'));
            return;
        }

        $this->booking = $booking;
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

    public function initiatePayment(): void
    {
        $this->processing = true;
        $this->errorMessage = null;

        try {
            $result = app(ProcessPaymentAction::class)->execute(
                $this->booking,
                request()->ip(),
                request()->userAgent()
            );
            
            // Redirect to Paystack
            $this->redirect($result['authorization_url']);
        } catch (\Exception $e) {
            $this->processing = false;
            $this->errorMessage = 'Failed to initialize payment. Please try again.';
            \Log::error('Payment initialization failed: ' . $e->getMessage(), [
                'booking_id' => $this->booking->id,
            ]);
        }
    }
}; ?>

<div class="min-h-[70vh]">
    <!-- Header -->
    <div class="bg-white border-b">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center gap-4">
                <a href="{{ route('dashboard') }}" 
                   class="text-gray-500 hover:text-gray-700 transition"
                   wire:navigate>
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Complete Your Payment</h1>
                    <p class="text-gray-500 text-sm">Booking #{{ $booking->id }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Error Message -->
        @if($errorMessage)
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700">
                {{ $errorMessage }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Booking Summary -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-2xl shadow-md p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Booking Summary</h2>
                    
                    <!-- Vehicle Info -->
                    <div class="flex gap-4 pb-4 border-b">
                        <div class="w-24 h-18 bg-gray-200 rounded-lg overflow-hidden flex-shrink-0">
                            @if($this->vehicle?->image_url)
                                <img src="{{ Storage::url($this->vehicle->image_url) }}" 
                                     alt="{{ $this->vehicle->make }}"
                                     class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-400">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">
                                {{ $this->vehicle?->make }} {{ $this->vehicle?->model }}
                            </h3>
                            <p class="text-gray-500 text-sm">{{ $this->vehicle?->year }}</p>
                        </div>
                    </div>

                    <!-- Dates -->
                    <div class="grid grid-cols-2 gap-4 py-4 border-b">
                        <div>
                            <p class="text-sm text-gray-500">Pick-up Date</p>
                            <p class="font-medium">{{ $booking->start_time->format('D, M d, Y') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Return Date</p>
                            <p class="font-medium">{{ $booking->end_time->format('D, M d, Y') }}</p>
                        </div>
                    </div>

                    <!-- Price Breakdown -->
                    <div class="pt-4 space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Daily Rate</span>
                            <span>₦{{ number_format($this->vehicle?->daily_rate) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Duration</span>
                            <span>{{ $this->totalDays }} {{ Str::plural('day', $this->totalDays) }}</span>
                        </div>
                        <div class="flex justify-between font-semibold text-lg pt-2 border-t">
                            <span>Total</span>
                            <span class="text-[#1E3A5F]">₦{{ number_format($booking->total_price) }}</span>
                        </div>
                    </div>
                </div>

                @if($booking->notes)
                    <div class="bg-white rounded-2xl shadow-md p-6">
                        <h3 class="font-semibold text-gray-900 mb-2">Notes</h3>
                        <p class="text-gray-600 text-sm">{{ $booking->notes }}</p>
                    </div>
                @endif
            </div>

            <!-- Payment Section -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-md p-6 sticky top-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Payment Method</h2>
                    
                    <!-- Paystack Option -->
                    <div class="border-2 border-[#1E3A5F] rounded-xl p-4 mb-6 bg-[#1E3A5F]/5">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-[#00C3F7] rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Paystack</p>
                                <p class="text-xs text-gray-500">Card, Bank Transfer, USSD</p>
                            </div>
                            <div class="ml-auto">
                                <div class="w-5 h-5 rounded-full border-2 border-[#1E3A5F] flex items-center justify-center">
                                    <div class="w-2.5 h-2.5 rounded-full bg-[#1E3A5F]"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total -->
                    <div class="bg-gray-50 rounded-xl p-4 mb-6">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Amount to Pay</span>
                            <span class="text-2xl font-bold text-[#1E3A5F]">₦{{ number_format($booking->total_price) }}</span>
                        </div>
                    </div>

                    <!-- Pay Button -->
                    <button 
                        wire:click="initiatePayment"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-50 cursor-not-allowed"
                        @if($processing) disabled @endif
                        class="w-full py-4 bg-[#FF6B35] text-white font-semibold rounded-xl hover:bg-[#e55a2b] transition flex items-center justify-center gap-2"
                    >
                        <span wire:loading.remove wire:target="initiatePayment">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </span>
                        <span wire:loading.remove wire:target="initiatePayment">Pay ₦{{ number_format($booking->total_price) }}</span>
                        <span wire:loading wire:target="initiatePayment" class="flex items-center gap-2">
                            <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Processing...
                        </span>
                    </button>

                    <!-- Secure Payment Notice -->
                    <div class="mt-4 flex items-center justify-center gap-2 text-xs text-gray-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        <span>Secured by Paystack</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
