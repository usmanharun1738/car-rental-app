<?php

use App\Models\Vehicle;
use App\Enums\VehicleStatus;
use App\Actions\Bookings\CheckVehicleAvailabilityAction;
use App\Actions\Bookings\CalculateBookingPriceAction;
use Livewire\Volt\Component;
use Livewire\Attributes\{Layout, Title};
use Carbon\Carbon;

new #[Layout('components.layouts.guest')] class extends Component
{
    public Vehicle $vehicle;
    
    public ?string $startDate = null;
    public ?string $endDate = null;
    public ?float $estimatedPrice = null;
    public ?string $availabilityMessage = null;
    public bool $isAvailable = false;

    public function mount(Vehicle $vehicle): void
    {
        $this->vehicle = $vehicle;
        
        // Set default dates (tomorrow to day after)
        $this->startDate = now()->addDay()->format('Y-m-d');
        $this->endDate = now()->addDays(2)->format('Y-m-d');
    }

    public function getTitle(): string
    {
        return "{$this->vehicle->make} {$this->vehicle->model} - CARTAR";
    }

    public function checkAvailability(): void
    {
        $this->validate([
            'startDate' => 'required|date|after_or_equal:today',
            'endDate' => 'required|date|after:startDate',
        ]);

        $start = Carbon::parse($this->startDate);
        $end = Carbon::parse($this->endDate);

        // Check availability
        $checker = app(CheckVehicleAvailabilityAction::class);
        $this->isAvailable = $checker->execute($this->vehicle, $start, $end);

        if ($this->isAvailable) {
            // Calculate price
            $calculator = app(CalculateBookingPriceAction::class);
            $this->estimatedPrice = $calculator->execute($this->vehicle, $start, $end);
            $this->availabilityMessage = null;
        } else {
            $this->estimatedPrice = null;
            $this->availabilityMessage = 'This vehicle is not available for the selected dates. Please try different dates.';
        }
    }

    public function proceedToBooking(): void
    {
        if (!auth()->check()) {
            session()->flash('message', 'Please log in to book a vehicle.');
            $this->redirect(route('login'));
            return;
        }

        if (!$this->isAvailable) {
            return;
        }

        // Redirect to booking wizard with parameters
        $this->redirect(route('booking.create', [
            'vehicle' => $this->vehicle->id,
            'start' => $this->startDate,
            'end' => $this->endDate,
        ]));
    }
}; ?>

<div>
    <!-- Breadcrumb -->
    <div class="bg-white border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <nav class="flex items-center gap-2 text-sm text-gray-500">
                <a href="/" class="hover:text-[#1E3A5F]" wire:navigate>Home</a>
                <span>/</span>
                <a href="/#vehicles" class="hover:text-[#1E3A5F]">Vehicles</a>
                <span>/</span>
                <span class="text-gray-900 font-medium">{{ $vehicle->make }} {{ $vehicle->model }}</span>
            </nav>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column: Vehicle Info -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Vehicle Image -->
                <div class="bg-white rounded-2xl shadow-md overflow-hidden">
                    <div class="relative h-64 md:h-96 bg-gray-200">
                        @if($vehicle->image_url)
                            <img 
                                src="{{ Storage::url($vehicle->image_url) }}" 
                                alt="{{ $vehicle->make }} {{ $vehicle->model }}"
                                class="w-full h-full object-cover"
                            >
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-400">
                                <svg class="w-24 h-24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                </svg>
                            </div>
                        @endif
                        
                        <!-- Status Badge -->
                        <span class="absolute top-4 right-4 px-4 py-2 text-sm font-semibold rounded-full 
                            {{ $vehicle->status === VehicleStatus::AVAILABLE ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800' }}">
                            {{ $vehicle->status->value }}
                        </span>
                    </div>
                </div>

                <!-- Vehicle Details -->
                <div class="bg-white rounded-2xl shadow-md p-6">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">
                        {{ $vehicle->make }} {{ $vehicle->model }}
                    </h1>
                    <p class="text-lg text-gray-500 mb-6">{{ $vehicle->year }} • {{ $vehicle->plate_number }}</p>

                    <!-- Features -->
                    @if($vehicle->features && count($vehicle->features) > 0)
                        <div class="mb-6">
                            <h2 class="text-lg font-semibold text-gray-900 mb-3">Features</h2>
                            <div class="flex flex-wrap gap-2">
                                @foreach($vehicle->features as $feature)
                                    <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm">
                                        {{ $feature }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Description -->
                    @if($vehicle->description)
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900 mb-3">Description</h2>
                            <p class="text-gray-600 leading-relaxed">{{ $vehicle->description }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Right Column: Booking Form -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-md p-6 sticky top-24">
                    <!-- Price -->
                    <div class="mb-6">
                        <span class="text-3xl font-bold text-[#1E3A5F]">₦{{ number_format($vehicle->daily_rate) }}</span>
                        <span class="text-gray-500">/day</span>
                    </div>

                    <!-- Date Selection -->
                    <form wire:submit.prevent="checkAvailability" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pick-up Date</label>
                            <input 
                                type="date" 
                                wire:model="startDate"
                                min="{{ now()->format('Y-m-d') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#FF6B35] focus:border-transparent"
                            >
                            @error('startDate') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Return Date</label>
                            <input 
                                type="date" 
                                wire:model="endDate"
                                min="{{ now()->addDay()->format('Y-m-d') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#FF6B35] focus:border-transparent"
                            >
                            @error('endDate') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <button 
                            type="submit"
                            class="w-full px-6 py-3 bg-[#1E3A5F] text-white font-semibold rounded-lg hover:bg-[#152a45] transition"
                        >
                            Check Availability
                        </button>
                    </form>

                    <!-- Availability Result -->
                    @if($availabilityMessage)
                        <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                            <p class="text-red-700 text-sm">{{ $availabilityMessage }}</p>
                        </div>
                    @endif

                    @if($isAvailable && $estimatedPrice)
                        <div class="mt-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                            <p class="text-green-700 font-medium mb-2">✓ Available!</p>
                            <div class="flex justify-between text-sm text-gray-600 mb-2">
                                <span>{{ Carbon::parse($startDate)->format('M d') }} - {{ Carbon::parse($endDate)->format('M d, Y') }}</span>
                                <span>{{ Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) }} days</span>
                            </div>
                            <div class="flex justify-between items-center pt-2 border-t border-green-200">
                                <span class="font-semibold text-gray-900">Total</span>
                                <span class="text-2xl font-bold text-[#1E3A5F]">₦{{ number_format($estimatedPrice) }}</span>
                            </div>
                        </div>

                        <button 
                            wire:click="proceedToBooking"
                            class="w-full mt-4 px-6 py-3 bg-[#FF6B35] text-white font-semibold rounded-lg hover:bg-[#e55a2b] transition flex items-center justify-center gap-2"
                        >
                            <span>Proceed to Book</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                            </svg>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
