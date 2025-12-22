<?php

use App\Models\Booking;
use App\Enums\BookingStatus;
use Livewire\Volt\Component;
use Livewire\Attributes\{Layout, Title, Computed};
use Livewire\WithPagination;

new #[Layout('components.layouts.dashboard')] #[Title('My Bookings - CARTAR')] class extends Component
{
    use WithPagination;

    public string $filter = 'all';

    public function setFilter(string $filter): void
    {
        $this->filter = $filter;
        $this->resetPage();
    }

    #[Computed]
    public function bookings()
    {
        $query = Booking::where('user_id', auth()->id())
            ->with(['vehicle.images'])
            ->latest();

        return match($this->filter) {
            'upcoming' => $query->whereIn('status', [BookingStatus::PENDING, BookingStatus::CONFIRMED])
                                ->where('start_time', '>', now())->get(),
            'past' => $query->where('status', BookingStatus::COMPLETED)->get(),
            'cancelled' => $query->where('status', BookingStatus::CANCELLED)->get(),
            default => $query->get(),
        };
    }

    #[Computed]
    public function upcomingBooking()
    {
        return Booking::where('user_id', auth()->id())
            ->whereIn('status', [BookingStatus::PENDING, BookingStatus::CONFIRMED])
            ->where('start_time', '>', now())
            ->with(['vehicle.images'])
            ->first();
    }

    public function cancelBooking(int $bookingId): void
    {
        $booking = Booking::where('user_id', auth()->id())->find($bookingId);
        
        if ($booking && $booking->status === BookingStatus::PENDING) {
            $booking->update(['status' => BookingStatus::CANCELLED]);
            session()->flash('success', 'Booking cancelled successfully.');
        }
    }

    public function getStatusBadge(BookingStatus $status): array
    {
        return match($status) {
            BookingStatus::PENDING => ['bg' => 'bg-amber-100', 'text' => 'text-amber-700', 'border' => 'border-amber-200'],
            BookingStatus::CONFIRMED => ['bg' => 'bg-green-100', 'text' => 'text-green-700', 'border' => 'border-green-200'],
            BookingStatus::ACTIVE => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'border' => 'border-blue-200'],
            BookingStatus::COMPLETED => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'border' => 'border-green-200'],
            BookingStatus::CANCELLED => ['bg' => 'bg-red-50', 'text' => 'text-red-600', 'border' => 'border-red-100'],
        };
    }
}; ?>

<x-slot:breadcrumb>My Bookings</x-slot:breadcrumb>

<div class="max-w-6xl mx-auto flex flex-col gap-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-[#111418]">My Bookings</h2>
            <p class="text-slate-500">View and manage your current and past car rentals.</p>
        </div>
        
        <!-- Filter Tabs -->
        <div class="flex items-center gap-1 bg-white p-1 rounded-lg border border-slate-200 shadow-sm self-start md:self-auto">
            <button wire:click="setFilter('all')" 
                    class="px-4 py-2 {{ $filter === 'all' ? 'bg-[#111418] text-white shadow-sm' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }} rounded-md text-sm font-bold transition-all">
                All Bookings
            </button>
            <button wire:click="setFilter('upcoming')"
                    class="px-4 py-2 {{ $filter === 'upcoming' ? 'bg-[#111418] text-white shadow-sm' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }} rounded-md text-sm font-medium transition-colors">
                Upcoming
            </button>
            <button wire:click="setFilter('past')"
                    class="px-4 py-2 {{ $filter === 'past' ? 'bg-[#111418] text-white shadow-sm' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }} rounded-md text-sm font-medium transition-colors">
                Past
            </button>
            <button wire:click="setFilter('cancelled')"
                    class="px-4 py-2 {{ $filter === 'cancelled' ? 'bg-[#111418] text-white shadow-sm' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }} rounded-md text-sm font-medium transition-colors">
                Cancelled
            </button>
        </div>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 flex items-center gap-3">
            <span class="material-symbols-outlined">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    <!-- Upcoming Trip Card -->
    @if($this->upcomingBooking && $filter === 'all')
        <div class="space-y-4">
            <h3 class="text-lg font-bold text-[#111418] flex items-center gap-2">
                <span class="material-symbols-outlined text-[#E3655B]">upcoming</span>
                Upcoming Trip
            </h3>
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden flex flex-col lg:flex-row group transition-all hover:shadow-md">
                <div class="w-full lg:w-72 h-48 lg:h-auto bg-gray-100 shrink-0 relative overflow-hidden">
                    @if($this->upcomingBooking->vehicle?->primary_image_url)
                        <img src="{{ $this->upcomingBooking->vehicle->primary_image_url }}" 
                             alt="{{ $this->upcomingBooking->vehicle->make }}"
                             class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <span class="material-symbols-outlined text-gray-400 text-5xl">directions_car</span>
                        </div>
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent lg:hidden"></div>
                    <div class="absolute bottom-4 left-4 text-white font-bold lg:hidden">
                        {{ $this->upcomingBooking->vehicle?->make }} {{ $this->upcomingBooking->vehicle?->model }}
                    </div>
                </div>
                <div class="flex-1 p-6 flex flex-col justify-between">
                    <div class="flex flex-col md:flex-row justify-between items-start gap-4">
                        <div>
                            <div class="flex items-center gap-3 mb-1">
                                <h4 class="text-xl font-bold text-[#111418] hidden lg:block">
                                    {{ $this->upcomingBooking->vehicle?->make }} {{ $this->upcomingBooking->vehicle?->model }}
                                </h4>
                                @php $badge = $this->getStatusBadge($this->upcomingBooking->status); @endphp
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $badge['bg'] }} {{ $badge['text'] }} {{ $badge['border'] }} border flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                    {{ $this->upcomingBooking->status->label() }}
                                </span>
                            </div>
                            <p class="text-slate-500 text-sm">{{ $this->upcomingBooking->vehicle?->transmission?->label() ?? 'Auto' }} • {{ $this->upcomingBooking->vehicle?->seats ?? 5 }} Seats</p>
                            <p class="text-xs text-slate-400 mt-1">Booking Ref: <span class="text-slate-600 font-mono">#TRV-{{ str_pad($this->upcomingBooking->id, 4, '0', STR_PAD_LEFT) }}</span></p>
                        </div>
                        <div class="text-left md:text-right w-full md:w-auto p-4 md:p-0 bg-slate-50 md:bg-transparent rounded-lg border md:border-none border-slate-100">
                            <p class="text-sm text-slate-400">Total Price</p>
                            <p class="text-2xl font-bold text-[#111418]">₦{{ number_format($this->upcomingBooking->total_price) }}</p>
                            <p class="text-xs text-[#E3655B] font-medium">
                                {{ $this->upcomingBooking->status === BookingStatus::PENDING ? 'Payment Pending' : 'Payment Completed' }}
                            </p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mt-6 py-6 border-t border-b border-slate-100">
                        <div class="flex gap-4">
                            <div class="w-10 h-10 rounded-full bg-slate-50 flex-shrink-0 flex items-center justify-center text-slate-400 border border-slate-100">
                                <span class="material-symbols-outlined text-lg">flight_land</span>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500 uppercase font-bold tracking-wide">Pick-up Location</p>
                                <p class="font-bold text-[#111418] mt-0.5">{{ $this->upcomingBooking->vehicle?->location ?? 'Lagos' }}</p>
                                <div class="flex items-center gap-1 text-sm text-slate-500 mt-1">
                                    <span class="material-symbols-outlined text-[16px]">schedule</span>
                                    {{ $this->upcomingBooking->start_time->format('M d, h:i A') }}
                                </div>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="w-10 h-10 rounded-full bg-slate-50 flex-shrink-0 flex items-center justify-center text-slate-400 border border-slate-100">
                                <span class="material-symbols-outlined text-lg">flight_takeoff</span>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500 uppercase font-bold tracking-wide">Drop-off Location</p>
                                <p class="font-bold text-[#111418] mt-0.5">{{ $this->upcomingBooking->vehicle?->location ?? 'Lagos' }}</p>
                                <div class="flex items-center gap-1 text-sm text-slate-500 mt-1">
                                    <span class="material-symbols-outlined text-[16px]">schedule</span>
                                    {{ $this->upcomingBooking->end_time->format('M d, h:i A') }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col sm:flex-row items-center justify-end gap-3 mt-5">
                        @if($this->upcomingBooking->status === BookingStatus::PENDING)
                            <button wire:click="cancelBooking({{ $this->upcomingBooking->id }})"
                                    wire:confirm="Are you sure you want to cancel this booking?"
                                    class="w-full sm:w-auto text-slate-500 hover:text-red-600 text-sm font-semibold px-4 py-2 transition-colors">
                                Cancel Booking
                            </button>
                            <a href="{{ route('booking.payment', $this->upcomingBooking) }}"
                               class="w-full sm:w-auto bg-[#E3655B] text-white px-6 py-2.5 rounded-lg text-sm font-bold hover:bg-[#d6554b] transition-colors shadow-sm shadow-orange-200 flex items-center justify-center gap-2">
                                <span class="material-symbols-outlined text-[18px]">credit_card</span>
                                Pay Now
                            </a>
                        @else
                            <a href="{{ route('booking.success', $this->upcomingBooking) }}"
                               class="w-full sm:w-auto bg-[#E3655B] text-white px-6 py-2.5 rounded-lg text-sm font-bold hover:bg-[#d6554b] transition-colors shadow-sm shadow-orange-200 flex items-center justify-center gap-2"
                               wire:navigate>
                                <span class="material-symbols-outlined text-[18px]">visibility</span>
                                View Details
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Bookings Table -->
    <div class="space-y-4 {{ $this->upcomingBooking && $filter === 'all' ? 'pt-4' : '' }}">
        @if($filter !== 'all' || !$this->upcomingBooking)
            <h3 class="text-lg font-bold text-[#111418] flex items-center gap-2">
                <span class="material-symbols-outlined text-slate-400">{{ $filter === 'upcoming' ? 'upcoming' : ($filter === 'past' ? 'history' : ($filter === 'cancelled' ? 'cancel' : 'list')) }}</span>
                {{ $filter === 'upcoming' ? 'Upcoming Trips' : ($filter === 'past' ? 'Past Rentals' : ($filter === 'cancelled' ? 'Cancelled Bookings' : 'All Bookings')) }}
            </h3>
        @else
            <h3 class="text-lg font-bold text-[#111418] flex items-center gap-2">
                <span class="material-symbols-outlined text-slate-400">history</span>
                Past Rentals
            </h3>
        @endif
        
        @if($this->bookings->isEmpty())
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-12 text-center">
                <span class="material-symbols-outlined text-slate-300 text-5xl mb-4">calendar_month</span>
                <h3 class="text-lg font-bold text-slate-900 mb-2">No bookings found</h3>
                <p class="text-slate-500 mb-6">You don't have any {{ $filter !== 'all' ? $filter : '' }} bookings yet.</p>
                <a href="{{ route('vehicles.index') }}" 
                   class="inline-flex items-center gap-2 px-6 py-3 bg-[#E3655B] text-white font-bold rounded-lg hover:bg-[#d6554b] transition"
                   wire:navigate>
                    <span class="material-symbols-outlined">directions_car</span>
                    Browse Vehicles
                </a>
            </div>
        @else
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left whitespace-nowrap">
                        <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-100">
                            <tr>
                                <th class="px-6 py-4 font-semibold">Car Details</th>
                                <th class="px-6 py-4 font-semibold">Location</th>
                                <th class="px-6 py-4 font-semibold">Dates</th>
                                <th class="px-6 py-4 font-semibold">Total Cost</th>
                                <th class="px-6 py-4 font-semibold">Status</th>
                                <th class="px-6 py-4 font-semibold text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($this->bookings as $booking)
                                @if($filter === 'all' && $this->upcomingBooking && $booking->id === $this->upcomingBooking->id)
                                    @continue
                                @endif
                                <tr class="hover:bg-slate-50/50 transition-colors {{ $booking->status === BookingStatus::CANCELLED ? 'opacity-60' : '' }}">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-12 h-12 rounded-lg bg-gray-100 overflow-hidden shadow-sm border border-slate-100 {{ $booking->status === BookingStatus::CANCELLED ? 'grayscale' : '' }}">
                                                @if($booking->vehicle?->primary_image_url)
                                                    <img src="{{ $booking->vehicle->primary_image_url }}" class="w-full h-full object-cover">
                                                @else
                                                    <div class="w-full h-full flex items-center justify-center">
                                                        <span class="material-symbols-outlined text-gray-400">directions_car</span>
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <p class="font-bold text-[#111418]">{{ $booking->vehicle?->make }} {{ $booking->vehicle?->model }}</p>
                                                <p class="text-xs text-slate-500">REF: #TRV-{{ str_pad($booking->id, 4, '0', STR_PAD_LEFT) }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col">
                                            <span class="font-bold text-[#111418] text-xs px-2 py-0.5 bg-gray-100 rounded w-fit mb-1">{{ $booking->vehicle?->location ?? 'Lagos' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-slate-600">
                                        {{ $booking->start_time->format('M d') }} - {{ $booking->end_time->format('M d') }}
                                        <p class="text-xs text-slate-400">{{ $booking->start_time->diffInDays($booking->end_time) }} Days</p>
                                    </td>
                                    <td class="px-6 py-4 font-bold text-[#111418]">₦{{ number_format($booking->total_price) }}</td>
                                    <td class="px-6 py-4">
                                        @php $badge = $this->getStatusBadge($booking->status); @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badge['bg'] }} {{ $badge['text'] }} {{ $badge['border'] }} border">
                                            {{ $booking->status->label() }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            @if($booking->status === BookingStatus::COMPLETED || $booking->status === BookingStatus::CONFIRMED)
                                                <a href="{{ route('booking.success', $booking) }}" 
                                                   class="text-[#E3655B] font-bold text-xs hover:underline"
                                                   wire:navigate>
                                                    View
                                                </a>
                                                <span class="text-slate-300">|</span>
                                                <a href="{{ route('booking.receipt.download', $booking) }}" 
                                                   class="text-slate-600 font-bold text-xs hover:underline flex items-center gap-1">
                                                    <span class="material-symbols-outlined text-[14px]">download</span>
                                                    Receipt
                                                </a>
                                            @elseif($booking->status === BookingStatus::PENDING)
                                                <a href="{{ route('booking.payment', $booking) }}" 
                                                   class="text-[#E3655B] font-bold text-xs hover:underline">
                                                    Pay Now
                                                </a>
                                            @else
                                                <span class="text-slate-300 font-semibold text-xs">—</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</div>
