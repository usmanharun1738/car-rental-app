<?php

use App\Enums\VehicleStatus;
use App\Models\Vehicle;
use Livewire\Volt\Component;
use Livewire\Attributes\{Layout, Title};

new #[Layout('components.layouts.guest')] #[Title('CARTAR - Premium Car Rentals')] class extends Component
{
    public string $search = '';
    public string $location = '';

    public function with(): array
    {
        return [
            'vehicles' => Vehicle::query()
                ->with('images') // eager load for performance
                ->where('status', VehicleStatus::AVAILABLE)
                ->when($this->search, fn ($query) => 
                    $query->where(fn ($q) => 
                        $q->where('make', 'like', "%{$this->search}%")
                          ->orWhere('model', 'like', "%{$this->search}%")
                    )
                )
                ->latest()
                ->take(15)
                ->get(),
        ];
    }
}; ?>

<div>
    <!-- Hero Section -->
    <section class="relative flex min-h-[600px] flex-col justify-center overflow-hidden bg-cover bg-center bg-no-repeat py-20" 
             style="background-image: linear-gradient(rgba(0, 0, 0, 0.4) 0%, rgba(0, 0, 0, 0.6) 100%), url('https://images.unsplash.com/photo-1449965408869-eaa3f722e40d?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80');">
        <div class="container mx-auto px-4 lg:px-40 flex flex-col items-center text-center mb-10">
            <h1 class="text-white text-4xl md:text-6xl font-black leading-tight tracking-[-0.033em] mb-4">
                Find your drive. <br><span class="text-[#FF6B35]">Anywhere, Anytime.</span>
            </h1>
            <p class="text-gray-200 text-lg font-normal leading-normal max-w-2xl mx-auto">
                Premium rentals at unbeatable prices. Choose from our wide range of vehicles for your next adventure.
            </p>
        </div>
    </section>

    <!-- How It Works - Glassmorphism Design -->
    <section class="py-20 px-4 md:px-10 bg-gradient-to-br from-[#1E3A5F] via-[#2a4a73] to-[#1E3A5F] relative overflow-hidden">
        <!-- Background decorative elements -->
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute -top-40 -right-40 w-80 h-80 bg-[#FF6B35]/20 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-[#FF6B35]/10 rounded-full blur-3xl"></div>
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-white/5 rounded-full blur-3xl"></div>
        </div>
        
        <div class="max-w-[1200px] mx-auto relative z-10">
            <!-- Section Header -->
            <div class="text-center mb-16">
                <span class="inline-block px-4 py-1.5 rounded-full bg-white/10 backdrop-blur-sm text-[#FF6B35] text-sm font-semibold mb-4 border border-white/20">
                    Simple Process
                </span>
                <h2 class="text-white text-4xl md:text-5xl font-bold leading-tight mb-4">How it works</h2>
                <p class="text-white/70 text-lg max-w-xl mx-auto">Get on the road in just 3 simple steps. Fast, easy, and hassle-free.</p>
            </div>
            
            <!-- Steps Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Step 1 -->
                <div class="group relative animate-fade-in-up" style="animation-delay: 0.1s;">
                    <div class="relative p-8 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 hover:bg-white/15 hover:border-white/30 transition-all duration-500 hover:-translate-y-2 hover:shadow-2xl hover:shadow-[#FF6B35]/20">
                        <!-- Step Number -->
                        <div class="absolute -top-4 -left-4 w-12 h-12 rounded-full bg-gradient-to-br from-[#FF6B35] to-[#ff8c5a] flex items-center justify-center text-white font-bold text-lg shadow-lg shadow-[#FF6B35]/40 group-hover:scale-110 transition-transform duration-300">
                            01
                        </div>
                        
                        <!-- Icon -->
                        <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-[#FF6B35] to-[#ff8c5a] flex items-center justify-center mb-6 mx-auto shadow-lg shadow-[#FF6B35]/30 group-hover:shadow-[#FF6B35]/50 transition-all duration-300 group-hover:scale-105">
                            <span class="material-symbols-outlined text-white text-4xl group-hover:animate-pulse">calendar_add_on</span>
                        </div>
                        
                        <!-- Content -->
                        <h3 class="text-xl font-bold text-white mb-3 text-center">Book Your Car</h3>
                        <p class="text-white/60 text-sm text-center leading-relaxed">Choose your perfect car and book it online instantly with our secure platform.</p>
                    </div>
                </div>
                
                <!-- Step 2 -->
                <div class="group relative animate-fade-in-up" style="animation-delay: 0.2s;">
                    <div class="relative p-8 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 hover:bg-white/15 hover:border-white/30 transition-all duration-500 hover:-translate-y-2 hover:shadow-2xl hover:shadow-[#FF6B35]/20">
                        <!-- Step Number -->
                        <div class="absolute -top-4 -left-4 w-12 h-12 rounded-full bg-gradient-to-br from-[#FF6B35] to-[#ff8c5a] flex items-center justify-center text-white font-bold text-lg shadow-lg shadow-[#FF6B35]/40 group-hover:scale-110 transition-transform duration-300">
                            02
                        </div>
                        
                        <!-- Icon -->
                        <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-[#FF6B35] to-[#ff8c5a] flex items-center justify-center mb-6 mx-auto shadow-lg shadow-[#FF6B35]/30 group-hover:shadow-[#FF6B35]/50 transition-all duration-300 group-hover:scale-105">
                            <span class="material-symbols-outlined text-white text-4xl group-hover:animate-pulse">car_rental</span>
                        </div>
                        
                        <!-- Content -->
                        <h3 class="text-xl font-bold text-white mb-3 text-center">Pick Up</h3>
                        <p class="text-white/60 text-sm text-center leading-relaxed">Pick up your car at one of our many locations or get it delivered to you.</p>
                    </div>
                </div>
                
                <!-- Step 3 -->
                <div class="group relative animate-fade-in-up" style="animation-delay: 0.3s;">
                    <div class="relative p-8 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 hover:bg-white/15 hover:border-white/30 transition-all duration-500 hover:-translate-y-2 hover:shadow-2xl hover:shadow-[#FF6B35]/20">
                        <!-- Step Number -->
                        <div class="absolute -top-4 -left-4 w-12 h-12 rounded-full bg-gradient-to-br from-[#FF6B35] to-[#ff8c5a] flex items-center justify-center text-white font-bold text-lg shadow-lg shadow-[#FF6B35]/40 group-hover:scale-110 transition-transform duration-300">
                            03
                        </div>
                        
                        <!-- Icon -->
                        <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-[#FF6B35] to-[#ff8c5a] flex items-center justify-center mb-6 mx-auto shadow-lg shadow-[#FF6B35]/30 group-hover:shadow-[#FF6B35]/50 transition-all duration-300 group-hover:scale-105">
                            <span class="material-symbols-outlined text-white text-4xl group-hover:animate-pulse">directions_car</span>
                        </div>
                        
                        <!-- Content -->
                        <h3 class="text-xl font-bold text-white mb-3 text-center">Drive Away</h3>
                        <p class="text-white/60 text-sm text-center leading-relaxed">Hit the road and enjoy your trip. We've got you covered with 24/7 support.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Animation Styles -->
    <style>
        @keyframes fade-in-up {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .animate-fade-in-up {
            animation: fade-in-up 0.6s ease-out forwards;
            opacity: 0;
        }
    </style>

    <!-- Popular Vehicles with Search -->
    <section id="vehicles" class="py-16 px-4 md:px-10 bg-gray-50">
        <div class="max-w-[1200px] mx-auto">
            <!-- Search Form -->
            <div class="bg-white rounded-xl shadow-sm p-6 mb-10 border border-gray-100">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 items-end">
                    <!-- Location -->
                    <div class="lg:col-span-1">
                        <label class="flex flex-col w-full">
                            <p class="text-gray-900 text-sm font-bold leading-normal pb-2 flex items-center gap-2">
                                <span class="material-symbols-outlined text-[#FF6B35] text-lg">location_on</span>
                                Location
                            </p>
                            <div class="relative">
                                <select wire:model="location" class="form-input flex w-full resize-none overflow-hidden rounded-lg text-gray-900 focus:outline-0 focus:ring-2 focus:ring-[#FF6B35] border border-gray-200 bg-white h-12 placeholder:text-gray-500 p-3 pr-8 text-sm font-normal leading-normal transition-all appearance-none cursor-pointer">
                                    <option value="">Select a city</option>
                                    <option value="lagos">Lagos</option>
                                    <option value="abuja">Abuja</option>
                                    <option value="kaduna">Kaduna</option>
                                    <option value="port-harcourt">Port Harcourt</option>
                                </select>
                                <span class="material-symbols-outlined absolute right-3 top-3 text-gray-400 pointer-events-none">expand_more</span>
                            </div>
                        </label>
                    </div>
                    
                    <!-- Search -->
                    <div class="lg:col-span-1">
                        <label class="flex flex-col w-full">
                            <p class="text-gray-900 text-sm font-bold leading-normal pb-2 flex items-center gap-2">
                                <span class="material-symbols-outlined text-[#FF6B35] text-lg">search</span>
                                Search Vehicle
                            </p>
                            <input 
                                type="text" 
                                wire:model.live.debounce.300ms="search"
                                placeholder="e.g. Toyota, Mercedes..."
                                class="form-input flex w-full resize-none overflow-hidden rounded-lg text-gray-900 focus:outline-0 focus:ring-2 focus:ring-[#FF6B35] border border-gray-200 bg-white h-12 placeholder:text-gray-500 p-3 text-sm font-normal leading-normal transition-all"
                            >
                        </label>
                    </div>
                    
                    <!-- Search Button -->
                    <div class="lg:col-span-1">
                        <button wire:click="$refresh" class="flex w-full cursor-pointer items-center justify-center overflow-hidden rounded-lg h-12 bg-[#FF6B35] text-white text-base font-bold leading-normal tracking-[0.015em] hover:bg-[#e55a2b] shadow-lg shadow-orange-500/30 transition-all transform active:scale-95">
                            <span class="truncate">Search Cars</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Section Header -->
            <div class="flex justify-between items-end mb-10">
                <div>
                    <h2 class="text-gray-900 text-3xl font-bold leading-tight mb-2">Popular Vehicles</h2>
                    <p class="text-gray-500 text-base">Explore our most booked vehicles</p>
                </div>
                <a class="hidden md:flex items-center gap-1 text-[#FF6B35] font-bold hover:underline" href="{{ route('vehicles.index') }}" wire:navigate>
                    View all fleet <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </a>
            </div>
            
            @if($vehicles->isEmpty())
                <div class="text-center py-12 bg-white rounded-xl">
                    <span class="material-symbols-outlined text-gray-400 text-6xl mb-4">directions_car</span>
                    <p class="text-gray-500">No vehicles available at the moment.</p>
                    <p class="text-sm text-gray-400 mt-2">Check back soon or try a different search.</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($vehicles as $vehicle)
                        <div class="group bg-white border border-gray-100 rounded-xl overflow-hidden hover:shadow-lg hover:border-[#FF6B35]/30 transition-all duration-300 flex flex-col">
                            <!-- Vehicle Image -->
                            <div class="relative aspect-[4/3] bg-gray-50 overflow-hidden">
                                @if($vehicle->primary_image_url)
                                    <img 
                                        src="{{ $vehicle->primary_image_url }}" 
                                        alt="{{ $vehicle->make }} {{ $vehicle->model }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                    >
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <span class="material-symbols-outlined text-gray-400 text-6xl">directions_car</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Vehicle Info -->
                            <div class="p-5 flex flex-col flex-1">
                                <div class="mb-4">
                                    <div class="flex justify-between items-start mb-1">
                                        <h3 class="text-lg font-bold text-gray-900">{{ $vehicle->make }} {{ $vehicle->model }}</h3>
                                        <div class="flex items-center gap-1 text-xs font-bold bg-[#FF6B35]/20 text-[#FF6B35] px-2 py-1 rounded">
                                            {{ $vehicle->average_rating > 0 ? number_format($vehicle->average_rating, 1) : '4.8' }} <span class="material-symbols-outlined text-xs">star</span>
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
                                        <span>{{ $vehicle->transmission?->shortLabel() ?? 'Auto' }}</span>
                                    </div>
                                    <div class="flex items-center gap-2 text-sm text-gray-600">
                                        <span class="material-symbols-outlined text-lg text-gray-400">{{ $vehicle->fuel_type?->icon() ?? 'local_gas_station' }}</span>
                                        <span>{{ $vehicle->fuel_type?->label() ?? 'Petrol' }}</span>
                                    </div>
                                    <div class="flex items-center gap-2 text-sm text-gray-600">
                                        <span class="material-symbols-outlined text-lg text-gray-400">speed</span>
                                        <span>{{ $vehicle->mileage == 0 || !$vehicle->mileage ? 'Unlimited' : $vehicle->mileage . 'km' }}</span>
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
            @endif
            
            <!-- Mobile View All -->
            <div class="mt-8 text-center md:hidden">
                <button class="px-6 py-3 rounded-lg border border-gray-200 text-gray-900 font-bold text-sm">View All Fleet</button>
            </div>
        </div>
    </section>

    <!-- Promo Banner -->
    <div class="py-16 px-4 md:px-10 bg-white">
        <div class="max-w-[1200px] mx-auto rounded-2xl overflow-hidden bg-[#1E3A5F] relative">
            <div class="absolute inset-0 bg-gradient-to-r from-[#1E3A5F] via-[#1E3A5F]/80 to-transparent z-10"></div>
            <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1494976388531-d1058494cdd8?ixlib=rb-4.0.3&auto=format&fit=crop&w=1600&q=80')] bg-cover bg-right bg-no-repeat"></div>
            <div class="relative z-20 flex flex-col md:flex-row items-center justify-between p-8 md:p-16 gap-8">
                <div class="flex flex-col gap-4 max-w-lg text-left">
                    <span class="inline-block px-3 py-1 rounded-full bg-white/20 text-white text-xs font-bold w-fit">Weekend Special</span>
                    <h2 class="text-white text-3xl md:text-5xl font-black leading-tight">Save 15% on all weekend rentals!</h2>
                    <p class="text-white/90 text-lg">Plan your weekend getaway now and save big on premium vehicles. Limited time offer.</p>
                </div>
                <div class="flex-shrink-0">
                    <button class="bg-[#FF6B35] text-white hover:bg-[#e55a2b] font-bold text-lg px-8 py-4 rounded-lg shadow-lg transition-transform hover:-translate-y-1">
                        Get Deal
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Why Choose Us -->
    <section class="py-16 px-4 md:px-10 bg-gray-50">
        <div class="max-w-[1000px] mx-auto">
            <h2 class="text-gray-900 text-3xl font-bold text-center mb-12">Why choose CARTAR?</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-y-10 gap-x-16">
                <!-- Feature 1 -->
                <div class="flex gap-4">
                    <div class="flex-shrink-0 size-12 rounded-full bg-[#FF6B35] flex items-center justify-center text-white">
                        <span class="material-symbols-outlined">verified_user</span>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">Secure & Reliable</h3>
                        <p class="text-gray-500">All our vehicles are regularly inspected and insured. Travel with peace of mind knowing you're covered.</p>
                    </div>
                </div>
                <!-- Feature 2 -->
                <div class="flex gap-4">
                    <div class="flex-shrink-0 size-12 rounded-full bg-[#FF6B35] flex items-center justify-center text-white">
                        <span class="material-symbols-outlined">support_agent</span>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">24/7 Customer Support</h3>
                        <p class="text-gray-500">Our dedicated team is available around the clock to assist you with any questions or roadside assistance.</p>
                    </div>
                </div>
                <!-- Feature 3 -->
                <div class="flex gap-4">
                    <div class="flex-shrink-0 size-12 rounded-full bg-[#FF6B35] flex items-center justify-center text-white">
                        <span class="material-symbols-outlined">payments</span>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">Best Price Guarantee</h3>
                        <p class="text-gray-500">We offer the most competitive rates in the market. Find a lower price? We'll match it.</p>
                    </div>
                </div>
                <!-- Feature 4 -->
                <div class="flex gap-4">
                    <div class="flex-shrink-0 size-12 rounded-full bg-[#FF6B35] flex items-center justify-center text-white">
                        <span class="material-symbols-outlined">cancel</span>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">Free Cancellation</h3>
                        <p class="text-gray-500">Plans change. Cancel up to 24 hours before your pickup time for a full refund, no questions asked.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials (TODO: Make dynamic later) -->
    <section class="py-16 px-4 md:px-10 bg-white">
        <div class="max-w-[1200px] mx-auto text-center">
            <h2 class="text-gray-900 text-3xl font-bold leading-tight mb-10">What our clients say</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Testimonial 1 -->
                <div class="p-6 rounded-xl border border-gray-100 bg-gray-50 flex flex-col gap-4 text-left">
                    <div class="flex gap-1 text-[#FF6B35]">
                        <span class="material-symbols-outlined text-sm">star</span>
                        <span class="material-symbols-outlined text-sm">star</span>
                        <span class="material-symbols-outlined text-sm">star</span>
                        <span class="material-symbols-outlined text-sm">star</span>
                        <span class="material-symbols-outlined text-sm">star</span>
                    </div>
                    <p class="text-gray-900 font-medium text-lg leading-relaxed">"The booking process was incredibly smooth. The car was clean and exactly what I ordered. Highly recommend!"</p>
                    <div class="flex items-center gap-3 mt-auto pt-4">
                        <div class="size-10 rounded-full bg-[#1E3A5F] flex items-center justify-center text-white font-bold">
                            AO
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-900">Adaeze Okonkwo</p>
                            <p class="text-xs text-gray-500">Lagos, Nigeria</p>
                        </div>
                    </div>
                </div>
                <!-- Testimonial 2 -->
                <div class="p-6 rounded-xl border border-gray-100 bg-gray-50 flex flex-col gap-4 text-left">
                    <div class="flex gap-1 text-[#FF6B35]">
                        <span class="material-symbols-outlined text-sm">star</span>
                        <span class="material-symbols-outlined text-sm">star</span>
                        <span class="material-symbols-outlined text-sm">star</span>
                        <span class="material-symbols-outlined text-sm">star</span>
                        <span class="material-symbols-outlined text-sm">star_half</span>
                    </div>
                    <p class="text-gray-900 font-medium text-lg leading-relaxed">"Great prices and excellent customer service. I had a slight delay and they adjusted my pickup time without issue."</p>
                    <div class="flex items-center gap-3 mt-auto pt-4">
                        <div class="size-10 rounded-full bg-[#1E3A5F] flex items-center justify-center text-white font-bold">
                            CE
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-900">Chidi Eze</p>
                            <p class="text-xs text-gray-500">Abuja, Nigeria</p>
                        </div>
                    </div>
                </div>
                <!-- Testimonial 3 -->
                <div class="p-6 rounded-xl border border-gray-100 bg-gray-50 flex flex-col gap-4 text-left">
                    <div class="flex gap-1 text-[#FF6B35]">
                        <span class="material-symbols-outlined text-sm">star</span>
                        <span class="material-symbols-outlined text-sm">star</span>
                        <span class="material-symbols-outlined text-sm">star</span>
                        <span class="material-symbols-outlined text-sm">star</span>
                        <span class="material-symbols-outlined text-sm">star</span>
                    </div>
                    <p class="text-gray-900 font-medium text-lg leading-relaxed">"Best rental experience I've had. The app makes check-in super fast. Will definitely use again for my next business trip."</p>
                    <div class="flex items-center gap-3 mt-auto pt-4">
                        <div class="size-10 rounded-full bg-[#1E3A5F] flex items-center justify-center text-white font-bold">
                            OA
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-900">Olumide Adeyemi</p>
                            <p class="text-xs text-gray-500">Port Harcourt, Nigeria</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
