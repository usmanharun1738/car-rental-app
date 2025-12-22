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

new #[Layout('components.layouts.guest')] #[Title('Finalize Your Booking - CARTAR')] class extends Component
{
    // Booking data from URL
    public ?int $vehicleId = null;
    public ?string $startDate = null;
    public ?string $endDate = null;
    
    // Driver information (we'll use authenticated user data)
    public string $firstName = '';
    public string $lastName = '';
    public string $email = '';
    public string $phone = '';
    
    // Add-ons
    public bool $fullInsurance = false;
    public bool $childSeat = false;
    public bool $gpsNavigation = false;
    
    // Other
    public string $notes = '';
    public bool $agreedToTerms = false;
    
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
        
        // Pre-fill user info
        $user = auth()->user();
        if ($user) {
            $nameParts = explode(' ', $user->name, 2);
            $this->firstName = $nameParts[0] ?? '';
            $this->lastName = $nameParts[1] ?? '';
            $this->email = $user->email;
            $this->phone = $user->phone ?? '';
        }
    }

    #[Computed]
    public function vehicle(): ?Vehicle
    {
        return Vehicle::with('images')->find($this->vehicleId);
    }

    #[Computed]
    public function addOnsTotal(): float
    {
        $total = 0;
        if ($this->fullInsurance) $total += 14000 * $this->totalDays;
        if ($this->childSeat) $total += 8000 * $this->totalDays;
        if ($this->gpsNavigation) $total += 5000 * $this->totalDays;
        return $total;
    }

    #[Computed]
    public function grandTotal(): float
    {
        return $this->totalPrice + $this->addOnsTotal;
    }

    public function confirmBooking(): void
    {
        $this->validate([
            'firstName' => 'required|string|min:2',
            'lastName' => 'required|string|min:2',
            'email' => 'required|email',
            'phone' => 'required|string|min:10',
            'agreedToTerms' => 'accepted',
        ], [
            'agreedToTerms.accepted' => 'You must agree to the terms and conditions.',
        ]);

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

            // Build notes with add-ons info
            $addOnNotes = [];
            if ($this->fullInsurance) $addOnNotes[] = 'Full Protection Insurance';
            if ($this->childSeat) $addOnNotes[] = 'Child Safety Seat';
            if ($this->gpsNavigation) $addOnNotes[] = 'GPS Navigation';
            
            $fullNotes = $this->notes;
            if (!empty($addOnNotes)) {
                $fullNotes .= "\nAdd-ons: " . implode(', ', $addOnNotes);
            }

            // Create the booking using our Action
            $this->booking = app(CreateBookingAction::class)->execute(
                userId: auth()->id(),
                vehicleId: $this->vehicleId,
                startTime: $start,
                endTime: $end,
                notes: $fullNotes ?: null
            );

            $this->bookingConfirmed = true;

        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }
}; ?>

<div class="min-h-[70vh] bg-[#f6f7f8]">
    <!-- Breadcrumb -->
    <div class="bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 md:px-8 lg:px-12 py-4">
            <div class="flex items-center gap-2 text-sm text-gray-500">
                <a class="hover:text-[#E3655B] transition-colors flex items-center gap-1" href="{{ route('home') }}" wire:navigate>
                    <span class="material-symbols-outlined text-lg">search</span> Search
                </a>
                <span class="material-symbols-outlined text-base">chevron_right</span>
                <a class="hover:text-[#E3655B] transition-colors flex items-center gap-1" href="{{ route('vehicles.show', $this->vehicle) }}" wire:navigate>
                    <span class="material-symbols-outlined text-lg">directions_car</span> Select Car
                </a>
                <span class="material-symbols-outlined text-base">chevron_right</span>
                <span class="text-[#E3655B] font-semibold flex items-center gap-1">
                    <span class="material-symbols-outlined text-lg">edit_note</span> Booking Details
                </span>
                <span class="material-symbols-outlined text-base">chevron_right</span>
                <span class="text-gray-400">Confirmation</span>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 md:px-8 lg:px-12 py-8">
        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700">
                {{ session('error') }}
            </div>
        @endif

        @if(!$bookingConfirmed)
            <!-- Main Booking Form -->
            <div class="flex flex-col gap-2 mb-8">
                <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight text-gray-900">Finalize your booking</h1>
                <p class="text-gray-600 text-base md:text-lg">Review your itinerary and enter your details to secure this car.</p>
            </div>

            <div class="flex flex-col lg:flex-row gap-8 xl:gap-12">
                <!-- Left Column: Forms -->
                <div class="flex-1 flex flex-col gap-8 min-w-0">
                    
                    <!-- Section 1: Driver Information -->
                    <section class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 md:p-8">
                        <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-100">
                            <div class="flex items-center justify-center w-8 h-8 rounded-full bg-[#9CBF9B]/20 text-[#9CBF9B] text-sm font-bold">1</div>
                            <h2 class="text-xl font-bold text-gray-900">Driver Information</h2>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div class="flex flex-col gap-2">
                                <label class="text-sm font-semibold text-gray-700">First Name</label>
                                <input wire:model="firstName" type="text" 
                                    class="w-full h-12 px-4 rounded-lg border border-gray-200 bg-white focus:ring-2 focus:ring-[#E3655B] focus:border-[#E3655B] transition-all" 
                                    placeholder="e.g. John">
                                @error('firstName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div class="flex flex-col gap-2">
                                <label class="text-sm font-semibold text-gray-700">Last Name</label>
                                <input wire:model="lastName" type="text" 
                                    class="w-full h-12 px-4 rounded-lg border border-gray-200 bg-white focus:ring-2 focus:ring-[#E3655B] focus:border-[#E3655B] transition-all" 
                                    placeholder="e.g. Doe">
                                @error('lastName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div class="flex flex-col gap-2">
                                <label class="text-sm font-semibold text-gray-700">Email Address</label>
                                <input wire:model="email" type="email" 
                                    class="w-full h-12 px-4 rounded-lg border border-gray-200 bg-white focus:ring-2 focus:ring-[#E3655B] focus:border-[#E3655B] transition-all" 
                                    placeholder="john.doe@example.com">
                                @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div class="flex flex-col gap-2">
                                <label class="text-sm font-semibold text-gray-700">Phone Number</label>
                                <input wire:model="phone" type="tel" 
                                    class="w-full h-12 px-4 rounded-lg border border-gray-200 bg-white focus:ring-2 focus:ring-[#E3655B] focus:border-[#E3655B] transition-all" 
                                    placeholder="+234 800 000 0000">
                                @error('phone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="mt-4 flex items-start gap-2 text-sm text-gray-500 bg-gray-50 p-3 rounded-lg">
                            <span class="material-symbols-outlined text-[#9CBF9B] text-lg mt-0.5">info</span>
                            <p>Driver must be at least 25 years old to rent this vehicle without a young driver fee.</p>
                        </div>
                    </section>

                    <!-- Section 2: Customize Your Trip -->
                    <section class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 md:p-8">
                        <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-100">
                            <div class="flex items-center justify-center w-8 h-8 rounded-full bg-[#9CBF9B]/20 text-[#9CBF9B] text-sm font-bold">2</div>
                            <h2 class="text-xl font-bold text-gray-900">Customize Your Trip</h2>
                        </div>
                        <div class="space-y-4">
                            <!-- Full Protection Insurance -->
                            <label class="relative flex flex-col sm:flex-row items-start sm:items-center gap-4 p-4 rounded-xl border border-gray-200 hover:border-[#E3655B] cursor-pointer transition-colors group {{ $fullInsurance ? 'border-[#E3655B] bg-[#E3655B]/5' : '' }}">
                                <div class="flex items-center justify-center w-12 h-12 rounded-lg bg-gray-50 text-[#9CBF9B]">
                                    <span class="material-symbols-outlined text-2xl">security</span>
                                </div>
                                <div class="flex-1">
                                    <h3 class="font-bold text-gray-900 group-hover:text-[#E3655B] transition-colors">Full Protection Insurance</h3>
                                    <p class="text-sm text-gray-500 mt-1">Zero excess liability. Covers theft, damage, and personal injury.</p>
                                </div>
                                <div class="flex items-center gap-4 mt-2 sm:mt-0 w-full sm:w-auto justify-between sm:justify-end">
                                    <span class="text-sm font-bold text-gray-900">+₦14,000<span class="text-gray-500 font-normal">/day</span></span>
                                    <input wire:model.live="fullInsurance" type="checkbox" class="w-5 h-5 rounded border-gray-300 text-[#E3655B] focus:ring-[#E3655B] cursor-pointer">
                                </div>
                            </label>

                            <!-- Child Safety Seat -->
                            <label class="relative flex flex-col sm:flex-row items-start sm:items-center gap-4 p-4 rounded-xl border border-gray-200 hover:border-[#E3655B] cursor-pointer transition-colors group {{ $childSeat ? 'border-[#E3655B] bg-[#E3655B]/5' : '' }}">
                                <div class="flex items-center justify-center w-12 h-12 rounded-lg bg-gray-50 text-[#9CBF9B]">
                                    <span class="material-symbols-outlined text-2xl">child_care</span>
                                </div>
                                <div class="flex-1">
                                    <h3 class="font-bold text-gray-900 group-hover:text-[#E3655B] transition-colors">Child Safety Seat</h3>
                                    <p class="text-sm text-gray-500 mt-1">Suitable for children 9-18kg (approx 9 months to 4 years).</p>
                                </div>
                                <div class="flex items-center gap-4 mt-2 sm:mt-0 w-full sm:w-auto justify-between sm:justify-end">
                                    <span class="text-sm font-bold text-gray-900">+₦8,000<span class="text-gray-500 font-normal">/day</span></span>
                                    <input wire:model.live="childSeat" type="checkbox" class="w-5 h-5 rounded border-gray-300 text-[#E3655B] focus:ring-[#E3655B] cursor-pointer">
                                </div>
                            </label>

                            <!-- GPS Navigation -->
                            <label class="relative flex flex-col sm:flex-row items-start sm:items-center gap-4 p-4 rounded-xl border border-gray-200 hover:border-[#E3655B] cursor-pointer transition-colors group {{ $gpsNavigation ? 'border-[#E3655B] bg-[#E3655B]/5' : '' }}">
                                <div class="flex items-center justify-center w-12 h-12 rounded-lg bg-gray-50 text-[#9CBF9B]">
                                    <span class="material-symbols-outlined text-2xl">explore</span>
                                </div>
                                <div class="flex-1">
                                    <h3 class="font-bold text-gray-900 group-hover:text-[#E3655B] transition-colors">GPS Navigation System</h3>
                                    <p class="text-sm text-gray-500 mt-1">Pre-loaded maps with live traffic updates.</p>
                                </div>
                                <div class="flex items-center gap-4 mt-2 sm:mt-0 w-full sm:w-auto justify-between sm:justify-end">
                                    <span class="text-sm font-bold text-gray-900">+₦5,000<span class="text-gray-500 font-normal">/day</span></span>
                                    <input wire:model.live="gpsNavigation" type="checkbox" class="w-5 h-5 rounded border-gray-300 text-[#E3655B] focus:ring-[#E3655B] cursor-pointer">
                                </div>
                            </label>
                        </div>
                    </section>

                    <!-- Section 3: Payment -->
                    <section class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 md:p-8">
                        <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-100">
                            <div class="flex items-center justify-center w-8 h-8 rounded-full bg-[#9CBF9B]/20 text-[#9CBF9B] text-sm font-bold">3</div>
                            <h2 class="text-xl font-bold text-gray-900">Payment</h2>
                        </div>
                        
                        <div class="bg-gray-50 rounded-lg p-4 mb-6">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 bg-[#0BA4DB] rounded-lg flex items-center justify-center">
                                    <span class="text-white font-bold text-lg">P</span>
                                </div>
                                <div>
                                    <p class="font-bold text-gray-900">Pay with Paystack</p>
                                    <p class="text-sm text-gray-500">Secure payment powered by Paystack</p>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Notes -->
                        <div class="mb-6">
                            <label class="text-sm font-semibold text-gray-700 block mb-2">Additional Notes (Optional)</label>
                            <textarea wire:model="notes" rows="2" 
                                class="w-full px-4 py-3 rounded-lg border border-gray-200 bg-white focus:ring-2 focus:ring-[#E3655B] focus:border-[#E3655B] transition-all"
                                placeholder="Any special requests..."></textarea>
                        </div>

                        <div class="flex items-center gap-3 mb-6">
                            <input wire:model="agreedToTerms" type="checkbox" id="terms" 
                                class="w-5 h-5 rounded border-gray-300 text-[#E3655B] focus:ring-[#E3655B] cursor-pointer">
                            <label class="text-sm text-gray-600 cursor-pointer" for="terms">
                                I agree to the <a class="text-[#E3655B] hover:underline" href="#">Terms of Service</a> and <a class="text-[#E3655B] hover:underline" href="#">Privacy Policy</a>.
                            </label>
                        </div>
                        @error('agreedToTerms') <p class="text-red-500 text-xs mb-4">{{ $message }}</p> @enderror

                        <button wire:click="confirmBooking" 
                                wire:loading.attr="disabled"
                                wire:loading.class="opacity-50"
                                class="w-full h-14 bg-[#E3655B] hover:opacity-90 text-white text-lg font-bold rounded-xl shadow-lg shadow-[#E3655B]/30 transition-all flex items-center justify-center gap-2 transform active:scale-[0.98]">
                            <span class="material-symbols-outlined" wire:loading.remove>lock</span>
                            <span wire:loading.remove>Confirm & Pay ₦{{ number_format($this->grandTotal) }}</span>
                            <span wire:loading>Processing...</span>
                        </button>
                        <p class="text-center text-xs text-gray-400 mt-4 flex items-center justify-center gap-1">
                            <span class="material-symbols-outlined text-sm">security</span>
                            Payments are secured with 256-bit SSL encryption
                        </p>
                    </section>
                </div>

                <!-- Right Column: Vehicle Summary (Sticky Sidebar) -->
                <div class="lg:w-[380px] min-w-[320px] shrink-0">
                    <div class="sticky top-24 flex flex-col gap-6">
                        @if($this->vehicle)
                            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                                <!-- Vehicle Image -->
                                <div class="relative h-48 bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center p-6">
                                    @if($this->vehicle->primary_image_url)
                                        <img alt="{{ $this->vehicle->make }} {{ $this->vehicle->model }}" 
                                             class="w-full h-full object-contain drop-shadow-xl" 
                                             src="{{ $this->vehicle->primary_image_url }}">
                                    @else
                                        <span class="material-symbols-outlined text-gray-400 text-6xl">directions_car</span>
                                    @endif
                                    <div class="absolute top-4 left-4 bg-white/90 backdrop-blur px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider text-gray-800 border border-[#CFD186]">Premium</div>
                                </div>
                                <div class="p-6">
                                    <div class="flex justify-between items-start mb-4">
                                        <div>
                                            <h3 class="text-xl font-bold text-gray-900">{{ $this->vehicle->make }} {{ $this->vehicle->model }}</h3>
                                            <p class="text-sm text-gray-500">{{ $this->vehicle->year }} • {{ $this->vehicle->transmission?->label() ?? 'Auto' }}</p>
                                        </div>
                                        <div class="flex gap-1 text-gray-400">
                                            <span class="material-symbols-outlined" title="{{ $this->vehicle->seats ?? 5 }} Seats">group</span>
                                            <span class="text-xs font-medium self-center">{{ $this->vehicle->seats ?? 5 }}</span>
                                        </div>
                                    </div>
                                    
                                    <!-- Location & Dates -->
                                    <div class="space-y-4 relative pb-6 border-b border-gray-200 border-dashed">
                                        <div class="flex gap-3">
                                            <div class="mt-1 text-[#9CBF9B]">
                                                <span class="material-symbols-outlined text-xl">location_on</span>
                                            </div>
                                            <div>
                                                <p class="text-xs font-bold uppercase text-gray-400 tracking-wider">Location</p>
                                                <p class="font-bold text-gray-900 text-sm">{{ $this->vehicle->location ?? 'Lagos' }}</p>
                                                <p class="text-sm text-gray-500">{{ Carbon::parse($startDate)->format('D, M d') }} - {{ Carbon::parse($endDate)->format('D, M d') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Price Breakdown -->
                                    <div class="py-6 space-y-3">
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600">Car Rental Fee ({{ $totalDays }} {{ Str::plural('day', $totalDays) }})</span>
                                            <span class="font-medium text-gray-900">₦{{ number_format($totalPrice) }}</span>
                                        </div>
                                        @if($fullInsurance)
                                            <div class="flex justify-between text-sm">
                                                <span class="text-gray-600">Full Insurance</span>
                                                <span class="font-medium text-gray-900">₦{{ number_format(14000 * $totalDays) }}</span>
                                            </div>
                                        @endif
                                        @if($childSeat)
                                            <div class="flex justify-between text-sm">
                                                <span class="text-gray-600">Child Safety Seat</span>
                                                <span class="font-medium text-gray-900">₦{{ number_format(8000 * $totalDays) }}</span>
                                            </div>
                                        @endif
                                        @if($gpsNavigation)
                                            <div class="flex justify-between text-sm">
                                                <span class="text-gray-600">GPS Navigation</span>
                                                <span class="font-medium text-gray-900">₦{{ number_format(5000 * $totalDays) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Total -->
                                    <div class="pt-4 border-t border-gray-200 flex items-end justify-between">
                                        <span class="text-gray-500 text-sm font-medium">Total Price</span>
                                        <div class="text-right">
                                            <span class="block text-3xl font-black text-gray-900 leading-none">₦{{ number_format($this->grandTotal) }}</span>
                                            <span class="text-xs text-gray-400">NGN, includes all taxes</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Need Help -->
                            <div class="bg-gray-50 p-4 rounded-xl flex gap-3 items-start border border-gray-200">
                                <span class="material-symbols-outlined text-[#9CBF9B]">support_agent</span>
                                <div>
                                    <p class="text-sm font-bold text-gray-900">Need Help?</p>
                                    <p class="text-xs text-gray-500 mt-1">Call our support team 24/7 at <a class="text-[#E3655B] font-medium hover:underline" href="tel:+2348001234567">+234 800 123 4567</a></p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        @else
            <!-- Booking Confirmed - Redirect to Payment -->
            <div class="max-w-3xl mx-auto">
                <div class="mb-8 flex flex-col md:flex-row md:items-start justify-between gap-6 bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <div class="flex gap-4">
                        <div class="flex-shrink-0 mt-1">
                            <div class="size-12 rounded-full bg-[#9CBF9B]/20 flex items-center justify-center">
                                <span class="material-symbols-outlined text-[#9CBF9B]" style="font-size: 28px;">check_circle</span>
                            </div>
                        </div>
                        <div class="flex flex-col gap-2">
                            <h1 class="text-3xl font-black leading-tight tracking-[-0.033em]">Booking Created!</h1>
                            <p class="text-gray-500 text-base max-w-2xl">
                                Your booking has been created successfully. Please proceed to payment to confirm your reservation.
                            </p>
                        </div>
                    </div>
                    @if($booking)
                        <div class="flex flex-col items-start md:items-end gap-2">
                            <span class="text-sm text-gray-500 font-medium">Booking Number</span>
                            <div class="flex items-center justify-center rounded-lg h-10 px-4 bg-gray-100 text-[#E3655B] text-base font-bold border border-gray-200">
                                #{{ str_pad($booking->id, 4, '0', STR_PAD_LEFT) }}
                            </div>
                        </div>
                    @endif
                </div>

                @if($booking)
                    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-8">
                        <p class="text-amber-800 text-sm flex items-center gap-2">
                            <span class="material-symbols-outlined">warning</span>
                            <strong>Action Required:</strong> Your booking is pending payment. Please complete payment within 30 minutes to secure your reservation.
                        </p>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="{{ route('booking.payment', $booking) }}" 
                           class="px-8 py-4 bg-[#E3655B] text-white font-bold rounded-xl hover:opacity-90 transition flex items-center justify-center gap-2 text-lg shadow-lg shadow-[#E3655B]/30">
                            <span class="material-symbols-outlined">lock</span>
                            Proceed to Payment
                        </a>
                        <a href="{{ route('dashboard') }}" 
                           class="px-6 py-4 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition flex items-center justify-center gap-2"
                           wire:navigate>
                            Go to Dashboard
                        </a>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
