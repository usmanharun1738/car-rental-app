<?php

use App\Models\Booking;
use App\Enums\BookingStatus;
use Livewire\Volt\Component;
use Livewire\Attributes\{Layout, Title, Computed};
use Carbon\Carbon;

new #[Layout('components.layouts.dashboard')] #[Title('Dashboard - CARTAR')] class extends Component
{
    public int $currentRentalIndex = 0;

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
        $totalSpent = $bookings->where('status', BookingStatus::COMPLETED)->sum('total_price');
        
        // Count only non-expired active rentals
        $activeRentals = $bookings
            ->whereIn('status', [BookingStatus::CONFIRMED, BookingStatus::ACTIVE])
            ->filter(fn($booking) => $booking->end_time->isFuture())
            ->count();
        
        return [
            'totalSpent' => $totalSpent,
            'activeRentals' => $activeRentals,
            'rewardPoints' => (int) ($totalSpent / 1000) * 10,
        ];
    }

    #[Computed]
    public function activeBookings()
    {
        return $this->bookings
            ->whereIn('status', [BookingStatus::CONFIRMED, BookingStatus::ACTIVE])
            ->filter(fn($booking) => $booking->end_time->isFuture())
            ->values();
    }

    #[Computed]
    public function currentActiveBooking()
    {
        $bookings = $this->activeBookings;
        if ($bookings->isEmpty()) {
            return null;
        }
        // Ensure index is within bounds
        $this->currentRentalIndex = min($this->currentRentalIndex, $bookings->count() - 1);
        return $bookings[$this->currentRentalIndex] ?? $bookings->first();
    }

    public function nextRental(): void
    {
        $count = $this->activeBookings->count();
        if ($count > 1) {
            $this->currentRentalIndex = ($this->currentRentalIndex + 1) % $count;
        }
    }

    public function previousRental(): void
    {
        $count = $this->activeBookings->count();
        if ($count > 1) {
            $this->currentRentalIndex = ($this->currentRentalIndex - 1 + $count) % $count;
        }
    }

    public function goToRental(int $index): void
    {
        $count = $this->activeBookings->count();
        if ($index >= 0 && $index < $count) {
            $this->currentRentalIndex = $index;
        }
    }

    #[Computed]
    public function recentBookings()
    {
        return $this->bookings->take(3);
    }

    public function getStatusBadge(BookingStatus $status): array
    {
        return match($status) {
            BookingStatus::PENDING => ['bg' => 'bg-amber-100', 'text' => 'text-amber-700', 'label' => 'Pending'],
            BookingStatus::CONFIRMED => ['bg' => 'bg-green-100', 'text' => 'text-green-700', 'label' => 'Confirmed'],
            BookingStatus::ACTIVE => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'label' => 'Active'],
            BookingStatus::COMPLETED => ['bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'label' => 'Completed'],
            BookingStatus::CANCELLED => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'label' => 'Cancelled'],
        };
    }
}; ?>


<div class="max-w-5xl mx-auto flex flex-col gap-8">
    <!-- Welcome Section -->
    <div class="flex flex-col gap-1">
        <h2 class="text-2xl font-bold text-[#111418]">Welcome back, {{ auth()->user()->name }}! 👋</h2>
        <p class="text-slate-500">Here's what's happening with your rentals today.</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Stat 1: Total Spent -->
        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between h-32 relative overflow-hidden group">
            <div class="absolute right-0 top-0 w-24 h-24 bg-[#9CBF9B]/10 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
            <div>
                <p class="text-slate-500 text-sm font-medium mb-1">Total Spent</p>
                <h3 class="text-2xl font-bold text-[#111418]">₦{{ number_format($this->stats['totalSpent']) }}</h3>
            </div>
            <div class="flex items-center text-[#9CBF9B] text-xs font-bold bg-[#9CBF9B]/10 w-fit px-2 py-1 rounded">
                <span class="material-symbols-outlined text-sm mr-1">trending_up</span>
                Lifetime
            </div>
        </div>

        <!-- Stat 2: Active Rentals -->
        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between h-32 relative overflow-hidden group">
            <div class="absolute right-0 top-0 w-24 h-24 bg-[#CFD186]/10 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
            <div>
                <p class="text-slate-500 text-sm font-medium mb-1">Active Rentals</p>
                <h3 class="text-2xl font-bold text-[#111418]">{{ $this->stats['activeRentals'] }} {{ Str::plural('Car', $this->stats['activeRentals']) }}</h3>
            </div>
            @if($this->currentActiveBooking)
                <div class="text-[#E3655B] text-xs font-medium">
                    Ends {{ $this->currentActiveBooking->end_time->diffForHumans() }}
                </div>
            @else
                <div class="text-slate-400 text-xs font-medium">No active rentals</div>
            @endif
        </div>

        <!-- Stat 3: Reward Points -->
        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between h-32 relative overflow-hidden group">
            <div class="absolute right-0 top-0 w-24 h-24 bg-blue-50 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
            <div>
                <p class="text-slate-500 text-sm font-medium mb-1">Reward Points</p>
                <h3 class="text-2xl font-bold text-[#111418]">{{ number_format($this->stats['rewardPoints']) }} pts</h3>
            </div>
            <div class="flex items-center text-slate-400 text-xs font-medium">
                <span class="material-symbols-outlined text-sm mr-1">star</span>
                Basic Member
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column: Active Rental & Booking History -->
        <div class="lg:col-span-2 flex flex-col gap-8">
            <!-- Active Rental Card with Carousel -->
            @if($this->currentActiveBooking)
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="flex items-center justify-between p-5 border-b border-slate-100 bg-white">
                        <div class="flex items-center gap-3">
                            <h3 class="font-bold text-lg text-[#111418]">Current Rental</h3>
                            @if($this->activeBookings->count() > 1)
                                <span class="text-xs text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full">
                                    {{ $currentRentalIndex + 1 }} of {{ $this->activeBookings->count() }}
                                </span>
                            @endif
                        </div>
                        <div class="flex items-center gap-3">
                            <!-- Carousel Navigation Arrows -->
                            @if($this->activeBookings->count() > 1)
                                <div class="flex items-center gap-1">
                                    <button wire:click="previousRental" 
                                            class="w-8 h-8 rounded-full border border-slate-200 flex items-center justify-center hover:bg-slate-50 hover:border-slate-300 transition-colors">
                                        <span class="material-symbols-outlined text-sm text-slate-600">chevron_left</span>
                                    </button>
                                    <button wire:click="nextRental" 
                                            class="w-8 h-8 rounded-full border border-slate-200 flex items-center justify-center hover:bg-slate-50 hover:border-slate-300 transition-colors">
                                        <span class="material-symbols-outlined text-sm text-slate-600">chevron_right</span>
                                    </button>
                                </div>
                            @endif
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-[#9CBF9B]/20 text-[#2C5E2E] border border-[#9CBF9B]/30 flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#2C5E2E]"></span> Active
                            </span>
                        </div>
                    </div>
                    <div class="p-5 flex flex-col sm:flex-row gap-6">
                        <div class="w-full sm:w-1/3 aspect-video bg-gray-100 rounded-lg overflow-hidden border border-slate-100 shadow-inner">
                            @if($this->currentActiveBooking->vehicle?->primary_image_url)
                                <img src="{{ $this->currentActiveBooking->vehicle->primary_image_url }}" 
                                     alt="{{ $this->currentActiveBooking->vehicle->make }}"
                                     class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-400">
                                    <span class="material-symbols-outlined text-4xl">directions_car</span>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1 flex flex-col justify-between">
                            <div>
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <h4 class="text-xl font-bold text-[#111418]">
                                            {{ $this->currentActiveBooking->vehicle?->make }} {{ $this->currentActiveBooking->vehicle?->model }} {{ $this->currentActiveBooking->vehicle?->year }}
                                        </h4>
                                        <p class="text-slate-500 text-sm">
                                            {{ $this->currentActiveBooking->vehicle?->transmission?->label() ?? 'Auto' }} • {{ $this->currentActiveBooking->vehicle?->seats ?? 5 }} Seats
                                        </p>
                                    </div>
                                    <p class="font-bold text-[#111418]">₦{{ number_format($this->currentActiveBooking->vehicle?->daily_rate) }}<span class="text-slate-400 text-xs font-normal">/day</span></p>
                                </div>
                                <div class="flex gap-4 mt-4 mb-4">
                                    <div class="bg-slate-50 p-3 rounded-lg flex-1 border border-slate-100">
                                        <p class="text-xs text-slate-400 font-medium uppercase">Pick-up</p>
                                        <p class="text-sm font-semibold text-[#111418] mt-1">{{ $this->currentActiveBooking->vehicle?->location ?? 'Lagos' }}</p>
                                        <p class="text-xs text-slate-500 mt-1">{{ $this->currentActiveBooking->start_time->format('M d, h:i A') }}</p>
                                    </div>
                                    <div class="bg-slate-50 p-3 rounded-lg flex-1 border border-slate-100">
                                        <p class="text-xs text-slate-400 font-medium uppercase">Drop-off</p>
                                        <p class="text-sm font-semibold text-[#111418] mt-1">{{ $this->currentActiveBooking->vehicle?->location ?? 'Lagos' }}</p>
                                        <p class="text-xs text-slate-500 mt-1">{{ $this->currentActiveBooking->end_time->format('M d, h:i A') }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="flex gap-3 mt-2">
                                <a href="{{ route('booking.success', $this->currentActiveBooking) }}" 
                                   class="flex-1 bg-[#111418] text-white text-sm font-bold py-2.5 rounded-lg hover:bg-slate-800 transition-colors text-center"
                                   wire:navigate>
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                    <!-- Carousel Dots -->
                    @if($this->activeBookings->count() > 1)
                        <div class="flex items-center justify-center gap-2 pb-4">
                            @foreach($this->activeBookings as $index => $booking)
                                <button wire:click="goToRental({{ $index }})" 
                                        class="w-2 h-2 rounded-full transition-all {{ $currentRentalIndex === $index ? 'bg-[#E3655B] w-4' : 'bg-slate-300 hover:bg-slate-400' }}">
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif

            <!-- Recent Booking History Table -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="flex items-center justify-between p-5 border-b border-slate-100">
                    <h3 class="font-bold text-lg text-[#111418]">Booking History</h3>
                    <a href="{{ route('dashboard.bookings') }}" class="text-sm font-semibold text-[#E3655B] hover:underline" wire:navigate>View All</a>
                </div>
                @if($this->recentBookings->isEmpty())
                    <div class="p-8 text-center">
                        <span class="material-symbols-outlined text-slate-300 text-4xl mb-3">calendar_month</span>
                        <p class="text-slate-500">No bookings yet</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-100">
                                <tr>
                                    <th class="px-6 py-3 font-semibold">Car Model</th>
                                    <th class="px-6 py-3 font-semibold">Date</th>
                                    <th class="px-6 py-3 font-semibold">Cost</th>
                                    <th class="px-6 py-3 font-semibold">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($this->recentBookings as $booking)
                                    <tr class="hover:bg-slate-50/50 transition-colors">
                                        <td class="px-6 py-4 font-medium text-[#111418]">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-md bg-gray-100 overflow-hidden">
                                                    @if($booking->vehicle?->primary_image_url)
                                                        <img src="{{ $booking->vehicle->primary_image_url }}" class="w-full h-full object-cover">
                                                    @else
                                                        <div class="w-full h-full flex items-center justify-center">
                                                            <span class="material-symbols-outlined text-gray-400 text-sm">directions_car</span>
                                                        </div>
                                                    @endif
                                                </div>
                                                {{ $booking->vehicle?->make }} {{ $booking->vehicle?->model }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-slate-500">{{ $booking->start_time->format('M d, Y') }}</td>
                                        <td class="px-6 py-4 font-medium text-[#111418]">₦{{ number_format($booking->total_price) }}</td>
                                        <td class="px-6 py-4">
                                            @php $badge = $this->getStatusBadge($booking->status); @endphp
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badge['bg'] }} {{ $badge['text'] }}">
                                                {{ $badge['label'] }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <!-- Right Column: Quick Links & Profile -->
        <div class="lg:col-span-1 flex flex-col gap-8">
            <!-- Profile Completion -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                <h4 class="font-bold text-[#111418] mb-4">Complete your profile</h4>
                <div class="flex flex-col gap-4">
                    <div class="flex items-center gap-3">
                        <div class="size-6 rounded-full bg-green-100 text-green-600 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-sm">check</span>
                        </div>
                        <p class="text-sm text-slate-600 line-through decoration-slate-400">Verify Email Address</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="size-6 rounded-full {{ auth()->user()->phone ? 'bg-green-100 text-green-600' : 'border border-dashed border-slate-300 text-slate-400' }} flex items-center justify-center shrink-0">
                            @if(auth()->user()->phone)
                                <span class="material-symbols-outlined text-sm">check</span>
                            @endif
                        </div>
                        <p class="text-sm {{ auth()->user()->phone ? 'text-slate-600 line-through decoration-slate-400' : 'text-[#111418] font-medium' }}">Add Phone Number</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="size-6 rounded-full border border-dashed border-slate-300 text-slate-400 flex items-center justify-center shrink-0">
                        </div>
                        <p class="text-sm text-[#111418] font-medium">Complete First Booking</p>
                    </div>
                    <a href="{{ route('dashboard.profile') }}" 
                       class="mt-2 w-full py-2 border border-slate-200 rounded-lg text-sm font-bold text-[#111418] hover:bg-slate-50 transition-colors text-center"
                       wire:navigate>
                        Update Profile
                    </a>
                </div>
            </div>

            <!-- Promotion Card -->
            <div class="bg-[#CFD186]/20 rounded-xl border border-[#CFD186]/30 p-6 flex flex-col items-center text-center">
                <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mb-3 shadow-sm">
                    <span class="material-symbols-outlined text-[#E3655B] text-3xl">celebration</span>
                </div>
                <h4 class="font-bold text-[#111418] mb-1">Invite Friends</h4>
                <p class="text-sm text-slate-600 mb-4">Earn ₦5,000 credit for every friend who rents a car.</p>
                <button class="text-[#E3655B] text-sm font-bold hover:underline">Copy Invite Link</button>
            </div>

            <!-- Need Help -->
            <div class="bg-[#101922] rounded-xl p-6 text-white relative overflow-hidden shadow-lg">
                <div class="absolute right-0 bottom-0 w-32 h-32 bg-[#E3655B] rounded-full opacity-10 blur-2xl translate-x-10 translate-y-10"></div>
                <div class="relative z-10">
                    <div class="size-10 bg-white/10 rounded-lg flex items-center justify-center mb-4 backdrop-blur-sm">
                        <span class="material-symbols-outlined text-[#CFD186]">support_agent</span>
                    </div>
                    <h4 class="font-bold text-lg mb-2">Need help?</h4>
                    <p class="text-slate-400 text-sm mb-4">Our support team is available 24/7 to assist you.</p>
                    <a href="tel:+2348001234567" class="w-full bg-white text-[#111418] py-2.5 rounded-lg text-sm font-bold hover:bg-slate-100 transition-colors block text-center">
                        Contact Support
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
