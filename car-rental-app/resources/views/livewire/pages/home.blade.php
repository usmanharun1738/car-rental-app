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
                ->where('status', VehicleStatus::AVAILABLE)
                ->when($this->search, fn ($query) => 
                    $query->where(fn ($q) => 
                        $q->where('make', 'like', "%{$this->search}%")
                          ->orWhere('model', 'like', "%{$this->search}%")
                    )
                )
                ->latest()
                ->take(4)
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
        
        <!-- Search Form -->
        <div class="container mx-auto px-4 lg:px-20 relative z-10 mt-8">
            <div class="bg-white rounded-xl shadow-xl p-6 lg:p-8 max-w-5xl mx-auto border border-gray-100">
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
                        <a href="#vehicles" class="flex w-full cursor-pointer items-center justify-center overflow-hidden rounded-lg h-12 bg-[#FF6B35] text-white text-base font-bold leading-normal tracking-[0.015em] hover:bg-[#e55a2b] shadow-lg shadow-orange-500/30 transition-all transform active:scale-95">
                            <span class="truncate">Search Cars</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section class="py-16 px-4 md:px-10 bg-white">
        <div class="max-w-[1200px] mx-auto">
            <div class="text-center mb-12">
                <h2 class="text-gray-900 text-3xl font-bold leading-tight mb-3">How it works</h2>
                <p class="text-gray-500 text-base">Simple and fast. Get on the road in minutes.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Step 1 -->
                <div class="flex flex-col items-center text-center p-6 rounded-xl bg-gray-50">
                    <div class="size-16 rounded-full bg-[#FF6B35]/20 flex items-center justify-center mb-4">
                        <span class="material-symbols-outlined text-[#FF6B35] text-3xl">calendar_add_on</span>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Book your car</h3>
                    <p class="text-gray-500 text-sm">Choose your perfect car and book it online instantly with our secure platform.</p>
                </div>
                <!-- Step 2 -->
                <div class="flex flex-col items-center text-center p-6 rounded-xl bg-gray-50">
                    <div class="size-16 rounded-full bg-[#FF6B35]/20 flex items-center justify-center mb-4">
                        <span class="material-symbols-outlined text-[#FF6B35] text-3xl">car_rental</span>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Pick up</h3>
                    <p class="text-gray-500 text-sm">Pick up your car at one of our many locations or get it delivered to you.</p>
                </div>
                <!-- Step 3 -->
                <div class="flex flex-col items-center text-center p-6 rounded-xl bg-gray-50">
                    <div class="size-16 rounded-full bg-[#FF6B35]/20 flex items-center justify-center mb-4">
                        <span class="material-symbols-outlined text-[#FF6B35] text-3xl">directions_car</span>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Drive away</h3>
                    <p class="text-gray-500 text-sm">Hit the road and enjoy your trip. We've got you covered with 24/7 support.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Popular Vehicles -->
    <section id="vehicles" class="py-16 px-4 md:px-10 bg-gray-50">
        <div class="max-w-[1200px] mx-auto">
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
                        <div class="group flex flex-col gap-4 rounded-xl bg-white shadow-sm hover:shadow-md transition-all border border-transparent hover:border-[#FF6B35]/20 p-4">
                            <!-- Vehicle Image -->
                            <div class="aspect-[4/3] w-full rounded-lg bg-gray-100 relative overflow-hidden flex items-center justify-center">
                                @if($vehicle->image_url)
                                    <img 
                                        src="{{ Storage::url($vehicle->image_url) }}" 
                                        alt="{{ $vehicle->make }} {{ $vehicle->model }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                    >
                                @else
                                    <span class="material-symbols-outlined text-gray-400 text-5xl">directions_car</span>
                                @endif
                                <!-- Status Badge -->
                                <div class="absolute top-2 right-2 bg-white/90 backdrop-blur-sm px-2 py-1 rounded text-xs font-bold text-gray-900">
                                    {{ $vehicle->year }}
                                </div>
                            </div>
                            
                            <!-- Vehicle Info -->
                            <div class="flex flex-col gap-1">
                                <h3 class="text-gray-900 text-lg font-bold">{{ $vehicle->make }} {{ $vehicle->model }}</h3>
                                <p class="text-gray-500 text-sm">{{ $vehicle->transmission?->shortLabel() ?? 'Auto' }} • {{ $vehicle->seats ?? 5 }} Seats</p>
                            </div>
                            
                            <!-- Price & CTA -->
                            <div class="mt-auto flex items-center justify-between pt-2 border-t border-gray-100">
                                <p class="text-[#FF6B35] text-lg font-bold">
                                    ₦{{ number_format($vehicle->daily_rate) }}
                                    <span class="text-xs text-gray-400 font-normal">/day</span>
                                </p>
                                <a href="{{ route('vehicles.show', $vehicle) }}" 
                                   class="text-sm font-bold text-[#1E3A5F] hover:text-[#FF6B35] transition-colors"
                                   wire:navigate>
                                    Rent Now
                                </a>
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
