<?php

use App\Models\Vehicle;
use App\Models\Booking;
use App\Enums\BookingStatus;
use App\Actions\Bookings\CheckVehicleAvailabilityAction;
use App\Actions\Bookings\CalculateBookingPriceAction;
use App\Actions\Bookings\CreateBookingAction;
use Livewire\Volt\Component;
use Livewire\Attributes\{Layout, Title, Computed};
use Carbon\Carbon;

new #[Layout('components.layouts.guest')] #[Title('Complete Your Booking - CARTAR')] class extends Component
{
    // Booking data from URL
    public ?int $vehicleId = null;
    public ?string $startDate = null;
    public ?string $endDate = null;
    
    // Wizard state
    public int $step = 1;
    public string $notes = '';
    
    // Calculated values
    public ?float $totalPrice = null;
    public ?int $totalDays = null;
    public bool $bookingConfirmed = false;
    public ?Booking $booking = null;

    public function mount(): void
    {
        // Get from query parameters
        $this->vehicleId = request()->query('vehicle');
        $this->startDate = request()->query('start');
        $this->endDate = request()->query('end');

        // Validate required parameters
        if (!$this->vehicleId || !$this->startDate || !$this->endDate) {
            session()->flash('error', 'Invalid booking parameters. Please start again.');
            $this->redirect(route('home'));
            return;
        }

        // Calculate price
        $vehicle = Vehicle::find($this->vehicleId);
        if (!$vehicle) {
            session()->flash('error', 'Vehicle not found.');
            $this->redirect(route('home'));
            return;
        }

        $start = Carbon::parse($this->startDate);
        $end = Carbon::parse($this->endDate);
        
        $this->totalDays = max(1, $start->diffInDays($end));
        $this->totalPrice = app(CalculateBookingPriceAction::class)->execute($vehicle, $start, $end);
    }

    #[Computed]
    public function vehicle(): ?Vehicle
    {
        return Vehicle::find($this->vehicleId);
    }

    public function nextStep(): void
    {
        if ($this->step < 3) {
            $this->step++;
        }
    }

    public function previousStep(): void
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    public function confirmBooking(): void
    {
        try {
            // Re-check availability before creating
            $vehicle = $this->vehicle;
            $start = Carbon::parse($this->startDate);
            $end = Carbon::parse($this->endDate);

            $isAvailable = app(CheckVehicleAvailabilityAction::class)->execute($vehicle, $start, $end);
            
            if (!$isAvailable) {
                session()->flash('error', 'Sorry, this vehicle is no longer available for the selected dates.');
                return;
            }

            // Create the booking using our Action
            $this->booking = app(CreateBookingAction::class)->execute(
                userId: auth()->id(),
                vehicleId: $this->vehicleId,
                startTime: $start,
                endTime: $end,
                notes: $this->notes ?: null
            );

            $this->bookingConfirmed = true;
            $this->step = 3;

        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }
}; ?>

<div class="min-h-[70vh]">
    <!-- Progress Steps -->
    <div class="bg-white border-b">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                @foreach([1 => 'Review', 2 => 'Confirm', 3 => 'Complete'] as $num => $label)
                    <div class="flex items-center">
                        <div class="flex items-center justify-center w-10 h-10 rounded-full 
                            {{ $step >= $num ? 'bg-[#1E3A5F] text-white' : 'bg-gray-200 text-gray-500' }}">
                            @if($step > $num)
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            @else
                                {{ $num }}
                            @endif
                        </div>
                        <span class="ml-3 text-sm font-medium {{ $step >= $num ? 'text-gray-900' : 'text-gray-500' }}">
                            {{ $label }}
                        </span>
                    </div>
                    @if($num < 3)
                        <div class="flex-1 mx-4 h-0.5 {{ $step > $num ? 'bg-[#1E3A5F]' : 'bg-gray-200' }}"></div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Flash Messages -->
        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700">
                {{ session('error') }}
            </div>
        @endif

        <!-- Step 1: Review Booking -->
        @if($step === 1)
            <div class="bg-white rounded-2xl shadow-md p-6 md:p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Review Your Booking</h2>

                @if($this->vehicle)
                    <!-- Vehicle Summary -->
                    <div class="flex gap-6 mb-8 pb-8 border-b">
                        <div class="w-32 h-24 bg-gray-200 rounded-lg overflow-hidden flex-shrink-0">
                            @if($this->vehicle->image_url)
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
                            <h3 class="text-xl font-semibold text-gray-900">
                                {{ $this->vehicle->make }} {{ $this->vehicle->model }}
                            </h3>
                            <p class="text-gray-500">{{ $this->vehicle->year }}</p>
                            <p class="text-[#1E3A5F] font-medium mt-2">₦{{ number_format($this->vehicle->daily_rate) }}/day</p>
                        </div>
                    </div>

                    <!-- Booking Details -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Pick-up Date</label>
                            <p class="text-lg font-medium text-gray-900">{{ Carbon::parse($startDate)->format('D, M d, Y') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Return Date</label>
                            <p class="text-lg font-medium text-gray-900">{{ Carbon::parse($endDate)->format('D, M d, Y') }}</p>
                        </div>
                    </div>

                    <!-- Price Summary -->
                    <div class="bg-gray-50 rounded-xl p-6">
                        <h4 class="font-semibold text-gray-900 mb-4">Price Summary</h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Daily Rate</span>
                                <span>₦{{ number_format($this->vehicle->daily_rate) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Duration</span>
                                <span>{{ $totalDays }} {{ Str::plural('day', $totalDays) }}</span>
                            </div>
                            <div class="flex justify-between pt-2 border-t font-semibold text-lg">
                                <span>Total</span>
                                <span class="text-[#1E3A5F]">₦{{ number_format($totalPrice) }}</span>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="flex justify-between mt-8">
                    <a href="{{ route('vehicles.show', $this->vehicle) }}" 
                       class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition"
                       wire:navigate>
                        ← Back
                    </a>
                    <button wire:click="nextStep" 
                            class="px-8 py-3 bg-[#FF6B35] text-white font-semibold rounded-lg hover:bg-[#e55a2b] transition">
                        Continue →
                    </button>
                </div>
            </div>
        @endif

        <!-- Step 2: Confirm & Notes -->
        @if($step === 2)
            <div class="bg-white rounded-2xl shadow-md p-6 md:p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Confirm Your Booking</h2>

                <!-- Customer Info -->
                <div class="mb-8">
                    <h3 class="font-semibold text-gray-900 mb-4">Your Details</h3>
                    <div class="bg-gray-50 rounded-xl p-4">
                        <p class="font-medium">{{ auth()->user()->name }}</p>
                        <p class="text-gray-600">{{ auth()->user()->email }}</p>
                        @if(auth()->user()->phone)
                            <p class="text-gray-600">{{ auth()->user()->phone }}</p>
                        @endif
                    </div>
                </div>

                <!-- Notes -->
                <div class="mb-8">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Additional Notes (Optional)
                    </label>
                    <textarea 
                        wire:model="notes"
                        rows="3"
                        placeholder="Any special requests or notes..."
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#FF6B35] focus:border-transparent"
                    ></textarea>
                </div>

                <!-- Summary Card -->
                <div class="bg-[#1E3A5F] text-white rounded-xl p-6 mb-8">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-gray-300 text-sm">Total Amount</p>
                            <p class="text-3xl font-bold">₦{{ number_format($totalPrice) }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-gray-300 text-sm">{{ $totalDays }} {{ Str::plural('day', $totalDays) }}</p>
                            <p class="font-medium">{{ $this->vehicle?->make }} {{ $this->vehicle?->model }}</p>
                        </div>
                    </div>
                </div>

                <!-- Terms -->
                <div class="mb-8 text-sm text-gray-600">
                    <p>By confirming this booking, you agree to our terms and conditions. Payment will be required to complete your reservation.</p>
                </div>

                <div class="flex justify-between">
                    <button wire:click="previousStep" 
                            class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                        ← Back
                    </button>
                    <button wire:click="confirmBooking" 
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-50"
                            class="px-8 py-3 bg-[#FF6B35] text-white font-semibold rounded-lg hover:bg-[#e55a2b] transition flex items-center gap-2">
                        <span wire:loading.remove>Confirm Booking</span>
                        <span wire:loading>Processing...</span>
                    </button>
                </div>
            </div>
        @endif

        <!-- Step 3: Success -->
        @if($step === 3 && $bookingConfirmed)
            <div class="bg-white rounded-2xl shadow-md p-6 md:p-8 text-center">
                <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>

                <h2 class="text-2xl font-bold text-gray-900 mb-2">Booking Confirmed!</h2>
                <p class="text-gray-600 mb-8">Your booking has been successfully created.</p>

                @if($booking)
                    <div class="bg-gray-50 rounded-xl p-6 text-left mb-8">
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <p class="text-gray-500">Booking ID</p>
                                <p class="font-semibold">#{{ $booking->id }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Status</p>
                                <p class="font-semibold text-amber-600">{{ $booking->status->value }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Vehicle</p>
                                <p class="font-semibold">{{ $this->vehicle?->make }} {{ $this->vehicle?->model }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Total</p>
                                <p class="font-semibold text-[#1E3A5F]">₦{{ number_format($booking->total_price) }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-8">
                        <p class="text-amber-800 text-sm">
                            <strong>Next Step:</strong> Please proceed to make payment to confirm your reservation.
                        </p>
                    </div>
                @endif

                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('dashboard') }}" 
                       class="px-6 py-3 bg-[#1E3A5F] text-white font-semibold rounded-lg hover:bg-[#152a45] transition"
                       wire:navigate>
                        Go to Dashboard
                    </a>
                    <a href="{{ route('home') }}" 
                       class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition"
                       wire:navigate>
                        Browse More Vehicles
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
