<?php

use App\Enums\VehicleStatus;
use App\Models\Vehicle;
use Livewire\Volt\Component;
use Livewire\Attributes\{Layout, Title};

new #[Layout('components.layouts.guest')] #[Title('CARTAR - Premium Car Rentals')] class extends Component
{
    public string $search = '';
    public string $vehicleType = '';

    public function with(): array
    {
        return [
            'vehicles' => Vehicle::query()
                ->where('status', VehicleStatus::AVAILABLE)
                ->when($this->search, fn ($query) => 
                    $query->where(fn ($q) => 
                        $q->where('make', 'like', "%{$this->search}%")
                          ->orWhere('model', 'like', "%{$this->search}%")
                    )
                )
                ->latest()
                ->take(6)
                ->get(),
        ];
    }
}; ?>

<div>
    <!-- Hero Section -->
    <section class="relative bg-gradient-to-br from-[#1E3A5F] to-[#152a45] text-white overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse">
                        <path d="M 40 0 L 0 0 0 40" fill="none" stroke="white" stroke-width="1"/>
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#grid)" />
            </svg>
        </div>
        
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 lg:py-32">
            <div class="text-center max-w-3xl mx-auto">
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6 leading-tight">
                    Drive Your <span class="text-[#FF6B35]">Dream Car</span> Today
                </h1>
                <p class="text-xl text-gray-300 mb-10">
                    Premium car rentals in Lagos. Book your perfect ride in minutes.
                </p>

                <!-- Search Form -->
                <div class="bg-white rounded-2xl shadow-2xl p-6 md:p-8 max-w-2xl mx-auto">
                    <div class="flex flex-col md:flex-row gap-4">
                        <div class="flex-1">
                            <label class="block text-left text-sm font-medium text-gray-700 mb-2">Search Vehicle</label>
                            <input 
                                type="text" 
                                wire:model.live.debounce.300ms="search"
                                placeholder="e.g. Toyota, Mercedes..."
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#FF6B35] focus:border-transparent text-gray-900"
                            >
                        </div>
                        <div class="flex items-end">
                            <a href="#vehicles" 
                               class="w-full md:w-auto px-8 py-3 bg-[#FF6B35] text-white font-semibold rounded-lg hover:bg-[#e55a2b] transition flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                Search
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section id="how-it-works" class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-center text-gray-900 mb-12">How It Works</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Step 1 -->
                <div class="text-center">
                    <div class="w-16 h-16 bg-[#FF6B35] rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-2xl font-bold text-white">1</span>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Choose Your Car</h3>
                    <p class="text-gray-600">Browse our premium selection of vehicles</p>
                </div>
                <!-- Step 2 -->
                <div class="text-center">
                    <div class="w-16 h-16 bg-[#FF6B35] rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-2xl font-bold text-white">2</span>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Book & Pay</h3>
                    <p class="text-gray-600">Select your dates and complete payment</p>
                </div>
                <!-- Step 3 -->
                <div class="text-center">
                    <div class="w-16 h-16 bg-[#FF6B35] rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-2xl font-bold text-white">3</span>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Drive Away</h3>
                    <p class="text-gray-600">Pick up your car and enjoy the ride</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Vehicle Grid -->
    <section id="vehicles" class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-3xl font-bold text-gray-900">Available Vehicles</h2>
                <a href="#" class="text-[#FF6B35] font-medium hover:underline">View All →</a>
            </div>

            @if($vehicles->isEmpty())
                <div class="text-center py-12 bg-white rounded-xl">
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    <p class="text-gray-500">No vehicles available at the moment.</p>
                    <p class="text-sm text-gray-400 mt-2">Check back soon or try a different search.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($vehicles as $vehicle)
                        <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition group">
                            <!-- Vehicle Image -->
                            <div class="relative h-48 bg-gray-200">
                                @if($vehicle->image_url)
                                    <img 
                                        src="{{ Storage::url($vehicle->image_url) }}" 
                                        alt="{{ $vehicle->make }} {{ $vehicle->model }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                                    >
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                                        <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                        </svg>
                                    </div>
                                @endif
                                <!-- Status Badge -->
                                <span class="absolute top-3 right-3 px-3 py-1 text-xs font-semibold rounded-full 
                                    {{ $vehicle->status === VehicleStatus::AVAILABLE ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800' }}">
                                    {{ $vehicle->status->value }}
                                </span>
                            </div>

                            <!-- Vehicle Info -->
                            <div class="p-5">
                                <h3 class="text-xl font-semibold text-gray-900">
                                    {{ $vehicle->make }} {{ $vehicle->model }}
                                </h3>
                                <p class="text-sm text-gray-500 mb-3">{{ $vehicle->year }}</p>

                                <div class="flex items-center justify-between">
                                    <div>
                                        <span class="text-2xl font-bold text-[#1E3A5F]">₦{{ number_format($vehicle->daily_rate) }}</span>
                                        <span class="text-sm text-gray-500">/day</span>
                                    </div>
                                    <a href="/vehicles/{{ $vehicle->id }}" 
                                       class="px-4 py-2 bg-[#1E3A5F] text-white text-sm font-medium rounded-lg hover:bg-[#152a45] transition"
                                       wire:navigate>
                                        Book Now
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
</div>
