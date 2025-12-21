<?php

use App\Enums\VehicleStatus;
use App\Models\Vehicle;
use Livewire\Volt\Component;
use Livewire\Attributes\{Layout, Title, Url};
use Livewire\WithPagination;

new #[Layout('components.layouts.guest')] #[Title('Available Vehicles - CARTAR')] class extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $location = '';

    #[Url]
    public int $minPrice = 0;

    #[Url]
    public int $maxPrice = 500000;

    #[Url]
    public string $sortBy = 'recommended';

    #[Url]
    public array $vehicleTypes = [];

    #[Url]
    public array $specifications = [];

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedVehicleTypes(): void
    {
        $this->resetPage();
    }

    public function updatedSpecifications(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->minPrice = 0;
        $this->maxPrice = 500000;
        $this->vehicleTypes = [];
        $this->specifications = [];
        $this->sortBy = 'recommended';
        $this->resetPage();
    }

    public function with(): array
    {
        $query = Vehicle::query()
            ->where('status', VehicleStatus::AVAILABLE)
            ->when($this->search, fn ($q) => 
                $q->where(fn ($subQ) => 
                    $subQ->where('make', 'like', "%{$this->search}%")
                          ->orWhere('model', 'like', "%{$this->search}%")
                )
            )
            ->when($this->minPrice > 0, fn ($q) => 
                $q->where('daily_rate', '>=', $this->minPrice)
            )
            ->when($this->maxPrice < 500000, fn ($q) => 
                $q->where('daily_rate', '<=', $this->maxPrice)
            );

        // Apply sorting
        $query = match($this->sortBy) {
            'price_low' => $query->orderBy('daily_rate', 'asc'),
            'price_high' => $query->orderBy('daily_rate', 'desc'),
            default => $query->latest(),
        };

        return [
            'vehicles' => $query->paginate(9),
            'totalCount' => Vehicle::where('status', VehicleStatus::AVAILABLE)->count(),
        ];
    }
}; ?>

<div>
    <!-- Breadcrumb -->
    <div class="bg-white border-b border-gray-100">
        <div class="max-w-[1440px] mx-auto px-4 md:px-10 py-4">
            <div class="flex flex-wrap gap-2 items-center">
                <a class="text-gray-500 text-sm font-medium hover:text-[#FF6B35] transition-colors" href="{{ route('home') }}" wire:navigate>Home</a>
                <span class="material-symbols-outlined text-gray-400 text-sm">chevron_right</span>
                <span class="text-gray-900 text-sm font-medium">All Vehicles</span>
            </div>
        </div>
    </div>

    <div class="max-w-[1440px] mx-auto px-4 md:px-10 py-6 pb-20">
        <!-- Search Bar -->
        <div class="bg-white rounded-xl p-4 shadow-sm mb-8 border border-gray-100">
            <div class="flex flex-col lg:flex-row gap-4 items-center">
                <div class="flex-1 w-full">
                    <label class="flex flex-col h-12 w-full">
                        <div class="flex w-full flex-1 items-stretch rounded-lg h-full border border-gray-200 overflow-hidden bg-gray-50 focus-within:ring-2 focus-within:ring-[#FF6B35]">
                            <div class="flex items-center justify-center pl-4 pr-2">
                                <span class="material-symbols-outlined text-gray-500">location_on</span>
                            </div>
                            <input 
                                wire:model.live.debounce.300ms="location"
                                class="flex w-full min-w-0 flex-1 resize-none bg-transparent text-gray-900 focus:outline-0 h-full placeholder:text-gray-500 px-2 text-base font-normal leading-normal" 
                                placeholder="Pick-up location"
                            >
                        </div>
                    </label>
                </div>
                <div class="flex-1 w-full">
                    <label class="flex flex-col h-12 w-full">
                        <div class="flex w-full flex-1 items-stretch rounded-lg h-full border border-gray-200 overflow-hidden bg-gray-50 focus-within:ring-2 focus-within:ring-[#FF6B35]">
                            <div class="flex items-center justify-center pl-4 pr-2">
                                <span class="material-symbols-outlined text-gray-500">search</span>
                            </div>
                            <input 
                                wire:model.live.debounce.300ms="search"
                                class="flex w-full min-w-0 flex-1 resize-none bg-transparent text-gray-900 focus:outline-0 h-full placeholder:text-gray-500 px-2 text-base font-normal leading-normal" 
                                placeholder="Search by make or model..."
                            >
                        </div>
                    </label>
                </div>
                <button 
                    wire:click="$refresh"
                    class="bg-[#FF6B35] hover:bg-[#e55a2b] text-white font-bold h-12 px-8 rounded-lg transition-colors w-full lg:w-auto"
                >
                    Search
                </button>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex flex-col lg:flex-row gap-8 items-start">
            <!-- Filters Sidebar -->
            <aside class="w-full lg:w-72 bg-white rounded-xl border border-gray-100 p-6 lg:sticky lg:top-20 shrink-0 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-gray-900 text-xl font-bold leading-tight">Filters</h2>
                    <button wire:click="resetFilters" class="text-sm text-[#FF6B35] font-medium hover:underline">Reset</button>
                </div>
                <hr class="border-gray-100 mb-6">

                <!-- Price Range -->
                <div class="mb-8">
                    <h3 class="text-gray-900 text-base font-bold mb-4">Price Range (per day)</h3>
                    <div class="flex items-center justify-between text-sm text-gray-500 mb-2">
                        <span>₦{{ number_format($minPrice) }}</span>
                        <span>₦{{ number_format($maxPrice) }}</span>
                    </div>
                    <input 
                        type="range" 
                        wire:model.live.debounce.300ms="maxPrice"
                        min="25000" 
                        max="500000" 
                        step="5000"
                        class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-[#FF6B35]"
                    >
                    <div class="mt-4 flex gap-3">
                        <div class="border rounded p-2 w-1/2 text-center text-sm font-medium bg-gray-50 text-gray-900">
                            ₦{{ number_format($minPrice) }}
                        </div>
                        <div class="border rounded p-2 w-1/2 text-center text-sm font-medium bg-gray-50 text-gray-900">
                            ₦{{ number_format($maxPrice) }}
                        </div>
                    </div>
                </div>

                <hr class="border-gray-100 mb-6">

                <!-- Car Type -->
                <div class="mb-8">
                    <h3 class="text-gray-900 text-base font-bold mb-4">Car Type</h3>
                    <div class="flex flex-col gap-2">
                        @foreach(['SUV', 'Sedan', 'Hatchback', 'Luxury', 'Convertible'] as $type)
                            <label class="flex items-center gap-3 py-1 cursor-pointer group">
                                <input 
                                    type="checkbox" 
                                    wire:model.live="vehicleTypes"
                                    value="{{ strtolower($type) }}"
                                    class="h-5 w-5 rounded border-gray-300 bg-transparent text-[#FF6B35] focus:ring-[#FF6B35] focus:ring-offset-0 transition"
                                >
                                <span class="text-gray-700 text-base font-normal group-hover:text-[#FF6B35] transition-colors">{{ $type }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <hr class="border-gray-100 mb-6">

                <!-- Specifications -->
                <div class="mb-4">
                    <h3 class="text-gray-900 text-base font-bold mb-4">Specifications</h3>
                    <div class="flex flex-col gap-2">
                        @foreach(['Automatic', 'Manual', 'Air Conditioning', 'Electric / Hybrid'] as $spec)
                            <label class="flex items-center gap-3 py-1 cursor-pointer group">
                                <input 
                                    type="checkbox" 
                                    wire:model.live="specifications"
                                    value="{{ strtolower($spec) }}"
                                    class="h-5 w-5 rounded border-gray-300 bg-transparent text-[#FF6B35] focus:ring-[#FF6B35] focus:ring-offset-0 transition"
                                >
                                <span class="text-gray-700 text-base font-normal group-hover:text-[#FF6B35] transition-colors">{{ $spec }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </aside>

            <!-- Vehicle Grid -->
            <div class="flex-1 w-full">
                <!-- Header -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $vehicles->total() }} Cars Available</h1>
                        <p class="text-sm text-gray-500 mt-1">Prices include taxes and fees</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-sm font-medium text-gray-600">Sort by:</span>
                        <div class="relative">
                            <select 
                                wire:model.live="sortBy"
                                class="appearance-none bg-white border border-gray-200 text-gray-900 py-2 pl-4 pr-10 rounded-lg text-sm font-medium focus:ring-2 focus:ring-[#FF6B35] focus:border-transparent cursor-pointer"
                            >
                                <option value="recommended">Recommended</option>
                                <option value="price_low">Price: Low to High</option>
                                <option value="price_high">Price: High to Low</option>
                            </select>
                            <span class="material-symbols-outlined absolute right-2 top-1/2 -translate-y-1/2 pointer-events-none text-gray-500 text-lg">expand_more</span>
                        </div>
                    </div>
                </div>

                <!-- Loading State -->
                <div wire:loading.delay class="fixed inset-0 bg-white/50 z-50 flex items-center justify-center">
                    <div class="bg-white p-4 rounded-lg shadow-lg flex items-center gap-3">
                        <svg class="animate-spin h-5 w-5 text-[#FF6B35]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="text-gray-700 font-medium">Loading...</span>
                    </div>
                </div>

                @if($vehicles->isEmpty())
                    <div class="text-center py-16 bg-white rounded-xl border border-gray-100">
                        <span class="material-symbols-outlined text-gray-400 text-6xl mb-4">directions_car</span>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">No vehicles found</h3>
                        <p class="text-gray-500 mb-4">Try adjusting your filters or search criteria.</p>
                        <button wire:click="resetFilters" class="px-6 py-2 bg-[#FF6B35] text-white font-bold rounded-lg hover:bg-[#e55a2b] transition-colors">
                            Reset Filters
                        </button>
                    </div>
                @else
                    <!-- Vehicle Cards Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                        @foreach($vehicles as $vehicle)
                            <div class="group bg-white border border-gray-100 rounded-xl overflow-hidden hover:shadow-lg hover:border-[#FF6B35]/30 transition-all duration-300 flex flex-col">
                                <!-- Vehicle Image -->
                                <div class="relative aspect-[4/3] bg-gray-50 overflow-hidden">
                                    @if($vehicle->image_url)
                                        <img 
                                            src="{{ Storage::url($vehicle->image_url) }}" 
                                            alt="{{ $vehicle->make }} {{ $vehicle->model }}"
                                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                        >
                                    @else
                                        <div class="w-full h-full flex items-center justify-center">
                                            <span class="material-symbols-outlined text-gray-400 text-6xl">directions_car</span>
                                        </div>
                                    @endif
                                    
                                    <!-- Status Badge -->
                                    @if($loop->first)
                                        <div class="absolute top-3 left-3 bg-amber-400/90 backdrop-blur px-2 py-1 rounded-md text-xs font-bold text-gray-900 shadow-sm">
                                            Best Value
                                        </div>
                                    @endif
                                    
                                    <!-- Favorite Button -->
                                    <button class="absolute top-3 right-3 p-2 bg-white/50 hover:bg-white rounded-full text-gray-600 hover:text-[#FF6B35] transition-colors backdrop-blur-sm">
                                        <span class="material-symbols-outlined text-base leading-none">favorite</span>
                                    </button>
                                </div>

                                <!-- Vehicle Info -->
                                <div class="p-5 flex flex-col flex-1">
                                    <div class="mb-4">
                                        <div class="flex justify-between items-start mb-1">
                                            <h3 class="text-lg font-bold text-gray-900">{{ $vehicle->make }} {{ $vehicle->model }}</h3>
                                            <div class="flex items-center gap-1 text-xs font-bold bg-[#FF6B35]/20 text-[#FF6B35] px-2 py-1 rounded">
                                                4.8 <span class="material-symbols-outlined text-xs">star</span>
                                            </div>
                                        </div>
                                        <p class="text-sm text-gray-500">{{ $vehicle->year }}</p>
                                    </div>

                                    <!-- Features Grid -->
                                    <div class="grid grid-cols-2 gap-y-3 gap-x-2 mb-6">
                                        <div class="flex items-center gap-2 text-sm text-gray-600">
                                            <span class="material-symbols-outlined text-lg text-gray-400">person</span>
                                            <span>{{ $vehicle->seats ?? 5 }} Seats</span>
                                        </div>
                                        <div class="flex items-center gap-2 text-sm text-gray-600">
                                            <span class="material-symbols-outlined text-lg text-gray-400">settings</span>
                                            <span>{{ $vehicle->transmission ?? 'Automatic' }}</span>
                                        </div>
                                        <div class="flex items-center gap-2 text-sm text-gray-600">
                                            <span class="material-symbols-outlined text-lg text-gray-400">work</span>
                                            <span>2 Bags</span>
                                        </div>
                                        <div class="flex items-center gap-2 text-sm text-gray-600">
                                            <span class="material-symbols-outlined text-lg text-gray-400">ac_unit</span>
                                            <span>AC</span>
                                        </div>
                                    </div>

                                    <!-- Price & CTA -->
                                    <div class="mt-auto pt-4 border-t border-gray-100 flex items-center justify-between">
                                        <div>
                                            <div class="flex items-baseline gap-1">
                                                <span class="text-xl font-bold text-gray-900">₦{{ number_format($vehicle->daily_rate) }}</span>
                                                <span class="text-xs text-gray-500">/ day</span>
                                            </div>
                                        </div>
                                        <a 
                                            href="{{ route('vehicles.show', $vehicle) }}"
                                            class="bg-[#FF6B35] hover:bg-[#e55a2b] text-white text-sm font-bold px-4 py-2.5 rounded-lg transition-colors shadow-sm hover:shadow-md"
                                            wire:navigate
                                        >
                                            View Deal
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    @if($vehicles->hasPages())
                        <div class="mt-12 flex justify-center">
                            {{ $vehicles->links() }}
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>
