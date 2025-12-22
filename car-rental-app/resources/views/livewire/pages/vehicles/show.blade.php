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
    public bool $includeInsurance = false;
    public int $rentalDays = 1;

    public function mount(Vehicle $vehicle): void
    {
        $this->vehicle = $vehicle;
        
        // Set default dates (tomorrow to day after)
        $this->startDate = now()->addDay()->format('Y-m-d');
        $this->endDate = now()->addDays(4)->format('Y-m-d');
        $this->calculateDays();
    }

    public function getTitle(): string
    {
        return "{$this->vehicle->make} {$this->vehicle->model} - CARTAR";
    }

    public function updatedStartDate(): void
    {
        $this->calculateDays();
        $this->resetAvailability();
    }

    public function updatedEndDate(): void
    {
        $this->calculateDays();
        $this->resetAvailability();
    }

    public function updatedIncludeInsurance(): void
    {
        if ($this->isAvailable) {
            $this->recalculatePrice();
        }
    }

    private function calculateDays(): void
    {
        if ($this->startDate && $this->endDate) {
            $start = Carbon::parse($this->startDate);
            $end = Carbon::parse($this->endDate);
            $this->rentalDays = max(1, $start->diffInDays($end));
        }
    }

    private function resetAvailability(): void
    {
        $this->isAvailable = false;
        $this->estimatedPrice = null;
        $this->availabilityMessage = null;
    }

    private function recalculatePrice(): void
    {
        $start = Carbon::parse($this->startDate);
        $end = Carbon::parse($this->endDate);
        $calculator = app(CalculateBookingPriceAction::class);
        $basePrice = $calculator->execute($this->vehicle, $start, $end);
        
        // Add insurance if selected (₦15,000 per day)
        $insuranceCost = $this->includeInsurance ? ($this->rentalDays * 15000) : 0;
        $this->estimatedPrice = $basePrice + $insuranceCost;
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
            $this->recalculatePrice();
            $this->availabilityMessage = null;
        } else {
            $this->estimatedPrice = null;
            $this->availabilityMessage = 'This vehicle is not available for the selected dates. Please try different dates.';
        }
    }

    public function proceedToBooking(): void
    {
        // Build the booking URL with parameters
        $bookingUrl = route('booking.create', [
            'vehicle' => $this->vehicle->id,
            'start' => $this->startDate,
            'end' => $this->endDate,
        ]);

        if (!auth()->check()) {
            // Store the intended URL so user is redirected back after login
            session()->put('url.intended', $bookingUrl);
            session()->flash('message', 'Please log in to book a vehicle.');
            $this->redirect(route('login'));
            return;
        }

        if (!$this->isAvailable) {
            return;
        }

        // Redirect to booking wizard
        $this->redirect($bookingUrl);
    }
}; ?>

<div>
    <!-- Breadcrumb -->
    <div class="bg-white border-b border-gray-100">
        <div class="max-w-[1200px] mx-auto px-4 md:px-8">
            <div class="flex flex-wrap gap-2 py-6 items-center">
                <a class="text-gray-500 hover:text-[#FF6B35] text-sm font-medium leading-normal transition-colors" href="{{ route('home') }}" wire:navigate>Home</a>
                <span class="material-symbols-outlined text-gray-400 text-sm">chevron_right</span>
                <a class="text-gray-500 hover:text-[#FF6B35] text-sm font-medium leading-normal transition-colors" href="{{ route('vehicles.index') }}" wire:navigate>All Cars</a>
                <span class="material-symbols-outlined text-gray-400 text-sm">chevron_right</span>
                <span class="text-gray-900 text-sm font-medium leading-normal">{{ $vehicle->make }} {{ $vehicle->model }}</span>
            </div>
        </div>
    </div>

    <div class="max-w-[1200px] mx-auto px-4 md:px-8 pb-20">
        <!-- Title & Actions -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-6 pt-6">
            <div>
                <h1 class="text-gray-900 tracking-tight text-3xl md:text-4xl font-bold leading-tight mb-2">
                    {{ $vehicle->make }} {{ $vehicle->model }}
                </h1>
                <div class="flex items-center gap-2">
                    <div class="flex text-[#FF6B35]">
                        <span class="material-symbols-outlined text-[20px]">star</span>
                        <span class="material-symbols-outlined text-[20px]">star</span>
                        <span class="material-symbols-outlined text-[20px]">star</span>
                        <span class="material-symbols-outlined text-[20px]">star</span>
                        <span class="material-symbols-outlined text-[20px]">star_half</span>
                    </div>
                    <span class="text-gray-900 text-sm font-medium">4.8 (124 reviews)</span>
                    <span class="text-gray-400 text-sm">•</span>
                    <span class="text-gray-500 text-sm">{{ $vehicle->year }}</span>
                </div>
            </div>
            <div class="flex gap-3">
                <button class="flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-200 hover:bg-gray-50 transition-colors">
                    <span class="material-symbols-outlined text-[#FF6B35] text-[20px]">share</span>
                    <span class="text-sm font-bold text-gray-900">Share</span>
                </button>
                <button class="flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-200 hover:bg-gray-50 transition-colors">
                    <span class="material-symbols-outlined text-[#FF6B35] text-[20px]">favorite_border</span>
                    <span class="text-sm font-bold text-gray-900">Save</span>
                </button>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <!-- Left Column: Vehicle Info -->
            <div class="lg:col-span-8 flex flex-col gap-8">
                <!-- Main Image & Gallery -->
                <div class="flex flex-col gap-4">
                    <!-- Main Image -->
                    <div class="w-full aspect-video rounded-xl overflow-hidden bg-gray-100 relative group">
                        @if($vehicle->primary_image_url)
                            <img 
                                src="{{ $vehicle->primary_image_url }}" 
                                alt="{{ $vehicle->make }} {{ $vehicle->model }}"
                                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                                id="mainVehicleImage"
                            >
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <span class="material-symbols-outlined text-gray-400 text-8xl">directions_car</span>
                            </div>
                        @endif
                        
                        <!-- Status Badge -->
                        <div class="absolute top-4 left-4 px-3 py-1.5 rounded-lg text-sm font-bold
                            {{ $vehicle->status === VehicleStatus::AVAILABLE ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800' }}">
                            {{ $vehicle->status->label() }}
                        </div>
                    </div>
                    
                    <!-- Image Thumbnails -->
                    @if($vehicle->images->count() > 1)
                        <div class="flex gap-2 overflow-x-auto pb-2">
                            @foreach($vehicle->images as $index => $image)
                                <button 
                                    onclick="document.getElementById('mainVehicleImage').src = '{{ $image->url }}'"
                                    class="flex-shrink-0 w-20 h-16 rounded-lg overflow-hidden border-2 transition-all hover:border-[#FF6B35] {{ $index === 0 ? 'border-[#FF6B35]' : 'border-gray-200' }}"
                                >
                                    <img 
                                        src="{{ $image->url }}" 
                                        alt="{{ $image->alt_text ?? $vehicle->make . ' ' . $vehicle->model }}"
                                        class="w-full h-full object-cover"
                                    >
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Vehicle Specs -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 p-6 bg-white rounded-xl border border-gray-100 shadow-sm">
                    <div class="flex flex-col gap-1">
                        <span class="material-symbols-outlined text-[#FF6B35] text-[24px] mb-1">{{ $vehicle->fuel_type?->icon() ?? 'local_gas_station' }}</span>
                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wide">Fuel Type</span>
                        <span class="text-sm font-bold text-gray-900">{{ $vehicle->fuel_type?->label() ?? 'Petrol' }}</span>
                    </div>
                    <div class="flex flex-col gap-1">
                        <span class="material-symbols-outlined text-[#FF6B35] text-[24px] mb-1">settings</span>
                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wide">Transmission</span>
                        <span class="text-sm font-bold text-gray-900">{{ $vehicle->transmission?->label() ?? 'Automatic' }}</span>
                    </div>
                    <div class="flex flex-col gap-1">
                        <span class="material-symbols-outlined text-[#FF6B35] text-[24px] mb-1">airline_seat_recline_extra</span>
                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wide">Seats</span>
                        <span class="text-sm font-bold text-gray-900">{{ $vehicle->seats ?? 5 }} Adults</span>
                    </div>
                    <div class="flex flex-col gap-1">
                        <span class="material-symbols-outlined text-[#FF6B35] text-[24px] mb-1">speed</span>
                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wide">Mileage</span>
                        <span class="text-sm font-bold text-gray-900">{{ $vehicle->mileage_display }}</span>
                    </div>
                </div>

                <!-- Vehicle Overview -->
                <div class="flex flex-col gap-6">
                    <h3 class="text-xl font-bold text-gray-900">Vehicle Overview</h3>
                    <p class="text-base text-gray-500 leading-relaxed">
                        @if($vehicle->description)
                            {{ $vehicle->description }}
                        @else
                            Experience the epitome of luxury and performance with the {{ $vehicle->make }} {{ $vehicle->model }}. 
                            This {{ $vehicle->year }} model offers a smooth, quiet ride with powerful performance that delivers whenever you need it. 
                            The interior is crafted with high-quality materials, featuring advanced technology to keep you connected and safe on the road. 
                            Perfect for business trips or a weekend getaway in style.
                        @endif
                    </p>

                    <!-- Key Features -->
                    <div class="mt-4">
                        <h4 class="text-lg font-bold text-gray-900 mb-4">Key Features</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-y-3 gap-x-8">
                            @forelse($vehicle->features as $feature)
                                <div class="flex items-center gap-3">
                                    <span class="material-symbols-outlined text-[#FF6B35] text-[20px]">check_circle</span>
                                    <span class="text-gray-700">{{ $feature->name }}</span>
                                </div>
                            @empty
                                <div class="col-span-2 text-gray-500 text-sm italic">
                                    No features listed for this vehicle.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <hr class="border-gray-200">

                <!-- Rental Terms -->
                <div class="flex flex-col gap-4">
                    <h3 class="text-xl font-bold text-gray-900">Rental Terms</h3>
                    <div class="grid md:grid-cols-3 gap-6">
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                            <div class="flex items-center gap-2 mb-2 text-gray-900 font-bold">
                                <span class="material-symbols-outlined text-[#FF6B35]">local_gas_station</span>
                                Full-to-Full
                            </div>
                            <p class="text-xs text-gray-500">The car is supplied with a full tank of fuel and should be returned full.</p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                            <div class="flex items-center gap-2 mb-2 text-gray-900 font-bold">
                                <span class="material-symbols-outlined text-[#FF6B35]">credit_card</span>
                                Deposit Required
                            </div>
                            <p class="text-xs text-gray-500">A security deposit of ₦50,000 will be held on your card at pickup.</p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                            <div class="flex items-center gap-2 mb-2 text-gray-900 font-bold">
                                <span class="material-symbols-outlined text-[#FF6B35]">event_available</span>
                                Free Cancellation
                            </div>
                            <p class="text-xs text-gray-500">Cancel for free up to 48 hours before your pick-up time.</p>
                        </div>
                    </div>
                </div>

                <hr class="border-gray-200">

                <!-- Ratings & Reviews -->
                <div class="flex flex-col gap-6">
                    <h3 class="text-xl font-bold text-gray-900">Ratings & Reviews</h3>
                    <div class="flex flex-wrap gap-x-8 gap-y-6 bg-white p-6 rounded-xl border border-gray-100 shadow-sm">
                        <div class="flex flex-col gap-2">
                            <p class="text-gray-900 text-5xl font-black leading-tight tracking-[-0.033em]">4.8</p>
                            <div class="flex gap-0.5 text-[#FF6B35]">
                                <span class="material-symbols-outlined">star</span>
                                <span class="material-symbols-outlined">star</span>
                                <span class="material-symbols-outlined">star</span>
                                <span class="material-symbols-outlined">star</span>
                                <span class="material-symbols-outlined">star_half</span>
                            </div>
                            <p class="text-gray-500 text-sm font-normal leading-normal">Based on 124 reviews</p>
                        </div>
                        <div class="grid min-w-[200px] max-w-[400px] flex-1 grid-cols-[20px_1fr_40px] items-center gap-y-3">
                            <p class="text-gray-900 text-sm font-medium">5</p>
                            <div class="flex h-2 flex-1 overflow-hidden rounded-full bg-gray-200"><div class="rounded-full bg-[#FF6B35]" style="width: 80%;"></div></div>
                            <p class="text-gray-500 text-sm font-normal text-right">80%</p>
                            <p class="text-gray-900 text-sm font-medium">4</p>
                            <div class="flex h-2 flex-1 overflow-hidden rounded-full bg-gray-200"><div class="rounded-full bg-[#FF6B35]" style="width: 12%;"></div></div>
                            <p class="text-gray-500 text-sm font-normal text-right">12%</p>
                            <p class="text-gray-900 text-sm font-medium">3</p>
                            <div class="flex h-2 flex-1 overflow-hidden rounded-full bg-gray-200"><div class="rounded-full bg-[#FF6B35]" style="width: 5%;"></div></div>
                            <p class="text-gray-500 text-sm font-normal text-right">5%</p>
                            <p class="text-gray-900 text-sm font-medium">2</p>
                            <div class="flex h-2 flex-1 overflow-hidden rounded-full bg-gray-200"><div class="rounded-full bg-[#FF6B35]" style="width: 1%;"></div></div>
                            <p class="text-gray-500 text-sm font-normal text-right">1%</p>
                            <p class="text-gray-900 text-sm font-medium">1</p>
                            <div class="flex h-2 flex-1 overflow-hidden rounded-full bg-gray-200"><div class="rounded-full bg-[#FF6B35]" style="width: 2%;"></div></div>
                            <p class="text-gray-500 text-sm font-normal text-right">2%</p>
                        </div>
                    </div>

                    <!-- Sample Review -->
                    <div class="flex flex-col gap-3 py-4 border-b border-gray-100">
                        <div class="flex justify-between items-start">
                            <div class="flex items-center gap-3">
                                <div class="bg-[#1E3A5F] h-10 w-10 rounded-full flex items-center justify-center text-white font-bold">JD</div>
                                <div>
                                    <p class="font-bold text-gray-900 text-sm">John Doe</p>
                                    <p class="text-xs text-gray-500">Oct 12, 2023</p>
                                </div>
                            </div>
                            <div class="flex text-[#FF6B35] text-sm">
                                <span class="material-symbols-outlined text-[16px]">star</span>
                                <span class="material-symbols-outlined text-[16px]">star</span>
                                <span class="material-symbols-outlined text-[16px]">star</span>
                                <span class="material-symbols-outlined text-[16px]">star</span>
                                <span class="material-symbols-outlined text-[16px]">star</span>
                            </div>
                        </div>
                        <p class="text-gray-700 text-sm leading-relaxed">
                            "The car was in pristine condition. The drive was smooth, and the tech features were amazing. Highly recommend for anyone looking for a premium experience."
                        </p>
                    </div>
                </div>
            </div>

            <!-- Right Column: Booking Sidebar -->
            <div class="lg:col-span-4 relative">
                <div class="sticky top-24 flex flex-col gap-6 bg-white p-6 rounded-xl shadow-lg border border-gray-100">
                    <!-- Price Header -->
                    <div class="flex justify-between items-end border-b border-gray-100 pb-4">
                        <div>
                            <span class="text-3xl font-bold text-gray-900">₦{{ number_format($vehicle->daily_rate) }}</span>
                            <span class="text-sm text-gray-500">/ day</span>
                        </div>
                        <div class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-bold">
                            Best Price
                        </div>
                    </div>

                    <!-- Booking Form -->
                    <form wire:submit.prevent="checkAvailability" class="flex flex-col gap-4">
                        <!-- Location -->
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-bold text-gray-500 uppercase">Pick-up Location</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="material-symbols-outlined text-gray-400">location_on</span>
                                </div>
                                <select class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-900 focus:ring-[#FF6B35] focus:border-[#FF6B35]">
                                    <option>Lagos - Murtala Muhammed Intl (LOS)</option>
                                    <option>Abuja - Nnamdi Azikiwe Intl (ABV)</option>
                                    <option>Port Harcourt Intl (PHC)</option>
                                </select>
                            </div>
                        </div>

                        <!-- Dates -->
                        <div class="grid grid-cols-2 gap-3">
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-bold text-gray-500 uppercase">Pick-up</label>
                                <input 
                                    type="date" 
                                    wire:model.live="startDate"
                                    min="{{ now()->format('Y-m-d') }}"
                                    class="w-full px-3 py-3 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-900 focus:ring-[#FF6B35] focus:border-[#FF6B35]"
                                >
                                @error('startDate') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-bold text-gray-500 uppercase">Drop-off</label>
                                <input 
                                    type="date" 
                                    wire:model.live="endDate"
                                    min="{{ now()->addDay()->format('Y-m-d') }}"
                                    class="w-full px-3 py-3 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-900 focus:ring-[#FF6B35] focus:border-[#FF6B35]"
                                >
                                @error('endDate') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Insurance Option -->
                        <label class="flex items-center justify-between p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50">
                            <div class="flex items-center gap-3">
                                <input 
                                    type="checkbox" 
                                    wire:model.live="includeInsurance"
                                    class="h-5 w-5 rounded border-gray-300 text-[#FF6B35] focus:ring-[#FF6B35]"
                                >
                                <span class="text-sm font-medium text-gray-900">Full Insurance</span>
                            </div>
                            <span class="text-sm font-bold text-gray-900">+₦15,000/day</span>
                        </label>

                        <!-- Check Availability Button -->
                        @if(!$isAvailable)
                            <button 
                                type="submit"
                                class="w-full py-4 bg-[#1E3A5F] hover:bg-[#152a45] text-white font-bold rounded-lg transition-all flex items-center justify-center gap-2"
                            >
                                <span>Check Availability</span>
                            </button>
                        @endif
                    </form>

                    <!-- Availability Error -->
                    @if($availabilityMessage)
                        <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
                            <p class="text-red-700 text-sm">{{ $availabilityMessage }}</p>
                        </div>
                    @endif

                    <!-- Price Breakdown (shown when available) -->
                    @if($isAvailable && $estimatedPrice)
                        <div class="bg-gray-50 rounded-lg p-4 flex flex-col gap-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Car Hire Charge ({{ $rentalDays }} days)</span>
                                <span class="text-gray-900 font-medium">₦{{ number_format($vehicle->daily_rate * $rentalDays) }}</span>
                            </div>
                            @if($includeInsurance)
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">Full Insurance ({{ $rentalDays }} days)</span>
                                    <span class="text-gray-900 font-medium">₦{{ number_format(15000 * $rentalDays) }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Taxes & Fees</span>
                                <span class="text-gray-900 font-medium">Included</span>
                            </div>
                            <div class="h-px bg-gray-200 my-1"></div>
                            <div class="flex justify-between items-center">
                                <span class="text-base font-bold text-gray-900">Total</span>
                                <span class="text-xl font-bold text-[#FF6B35]">₦{{ number_format($estimatedPrice) }}</span>
                            </div>
                        </div>

                        <button 
                            wire:click="proceedToBooking"
                            class="w-full py-4 bg-[#FF6B35] hover:bg-[#e55a2b] text-white font-bold rounded-lg transition-all shadow-lg shadow-orange-500/30 flex items-center justify-center gap-2"
                        >
                            <span>Book This Car</span>
                            <span class="material-symbols-outlined text-sm">arrow_forward</span>
                        </button>

                        <p class="text-center text-xs text-gray-500">
                            You won't be charged yet.
                        </p>
                    @endif

                    <!-- Trust Badges -->
                    <div class="mt-4 flex flex-col gap-3 px-2">
                        <div class="flex items-start gap-3">
                            <span class="material-symbols-outlined text-[#FF6B35] text-[20px] mt-0.5">verified_user</span>
                            <div>
                                <p class="text-xs font-bold text-gray-900">Price Match Guarantee</p>
                                <p class="text-[10px] text-gray-500">Found it cheaper? We'll match it.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="material-symbols-outlined text-[#FF6B35] text-[20px] mt-0.5">clean_hands</span>
                            <div>
                                <p class="text-xs font-bold text-gray-900">Enhanced Cleaning</p>
                                <p class="text-[10px] text-gray-500">This car is rigorously sanitized.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
