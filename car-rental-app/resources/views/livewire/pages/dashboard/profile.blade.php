<?php

use Livewire\Volt\Component;
use Livewire\Attributes\{Layout, Title, Validate};
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

new #[Layout('components.layouts.dashboard')] #[Title('User Profile - CARTAR')] class extends Component
{
    use WithFileUploads;

    #[Validate('required|string|max:255')]
    public string $firstName = '';

    #[Validate('required|string|max:255')]
    public string $lastName = '';

    #[Validate('required|email')]
    public string $email = '';

    #[Validate('nullable|string|max:20')]
    public ?string $phone = '';

    #[Validate('nullable|string|max:255')]
    public ?string $address = '';

    #[Validate('nullable|string|max:100')]
    public ?string $city = '';

    #[Validate('nullable|string|max:100')]
    public ?string $state = '';

    #[Validate('nullable|image|max:2048')]
    public $photo = null;

    public bool $bookingNotifications = true;
    public bool $marketingOffers = false;
    public bool $smsAlerts = true;

    public function mount(): void
    {
        $user = auth()->user();
        $names = explode(' ', $user->name, 2);
        
        $this->firstName = $names[0] ?? '';
        $this->lastName = $names[1] ?? '';
        $this->email = $user->email;
        $this->phone = $user->phone ?? '';
        $this->address = $user->address ?? '';
        $this->city = $user->city ?? '';
        $this->state = $user->state ?? '';
    }

    public function saveProfile(): void
    {
        $this->validate();

        $user = auth()->user();
        $user->update([
            'name' => trim($this->firstName . ' ' . $this->lastName),
            'phone' => $this->phone,
        ]);

        session()->flash('success', 'Profile updated successfully.');
    }

    public function uploadPhoto(): void
    {
        $this->validate(['photo' => 'required|image|max:2048']);

        $user = auth()->user();

        // Delete old photo if exists
        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }

        // Store new photo
        $path = $this->photo->store('profile-photos', 'public');
        $user->update(['profile_photo_path' => $path]);

        $this->photo = null;
        session()->flash('success', 'Profile photo updated successfully.');
    }

    public function deletePhoto(): void
    {
        $user = auth()->user();

        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
            $user->update(['profile_photo_path' => null]);
            session()->flash('success', 'Profile photo removed.');
        }
    }

    public function getProfileCompletion(): int
    {
        $steps = [
            auth()->user()->email_verified_at !== null,
            !empty(auth()->user()->phone),
            auth()->user()->bookings()->exists(),
        ];
        
        return (int) ((array_sum($steps) / count($steps)) * 100);
    }
}; ?>

<x-slot:breadcrumb>Profile</x-slot:breadcrumb>

<div class="max-w-5xl mx-auto flex flex-col gap-8">
    <!-- Header -->
    <div>
        <h2 class="text-2xl font-bold text-[#111418]">My Profile</h2>
        <p class="text-slate-500 mt-1">Manage your personal information, privacy, and security settings.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column: Profile Card & Completion -->
        <div class="lg:col-span-1 flex flex-col gap-6">
            <!-- Profile Card -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 flex flex-col items-center text-center relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-20 bg-gradient-to-r from-[#101922] to-[#2C3E50]"></div>
                <div class="relative z-10 -mt-2">
                    <div class="relative inline-block" x-data="{ showUpload: false }">
                        @if($photo)
                            <img src="{{ $photo->temporaryUrl() }}" class="size-24 rounded-full border-4 border-white shadow-md object-cover">
                        @elseif(auth()->user()->profile_photo_url)
                            <img src="{{ auth()->user()->profile_photo_url }}" class="size-24 rounded-full border-4 border-white shadow-md object-cover">
                        @else
                            <div class="size-24 rounded-full border-4 border-white shadow-md bg-[#E3655B] flex items-center justify-center text-white text-3xl font-bold">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                        @endif
                        <label class="absolute bottom-0 right-0 bg-[#E3655B] text-white p-1.5 rounded-full border-2 border-white shadow-sm hover:bg-[#d6554b] transition-colors cursor-pointer" title="Update Photo">
                            <input type="file" wire:model="photo" accept="image/*" class="hidden">
                            <span class="material-symbols-outlined text-[16px]">photo_camera</span>
                        </label>
                    </div>
                    @if($photo)
                        <div class="mt-3 flex gap-2 justify-center">
                            <button wire:click="uploadPhoto" class="px-3 py-1 bg-[#9CBF9B] text-white text-xs font-bold rounded-lg hover:bg-[#8aae89]">
                                Save Photo
                            </button>
                            <button wire:click="$set('photo', null)" class="px-3 py-1 bg-slate-200 text-slate-700 text-xs font-bold rounded-lg hover:bg-slate-300">
                                Cancel
                            </button>
                        </div>
                    @elseif(auth()->user()->profile_photo_url)
                        <button wire:click="deletePhoto" wire:confirm="Are you sure you want to remove your photo?" class="mt-2 text-red-500 text-xs font-medium hover:underline">
                            Remove Photo
                        </button>
                    @endif
                </div>
                <div class="mt-3">
                    <h3 class="text-lg font-bold text-[#111418]">{{ auth()->user()->name }}</h3>
                    <p class="text-slate-500 text-sm">{{ auth()->user()->email }}</p>
                </div>
                <div class="mt-4 flex gap-2 justify-center">
                    @if(auth()->user()->email_verified_at)
                        <span class="px-3 py-1 bg-[#9CBF9B]/20 text-[#2C5E2E] text-xs font-bold rounded-full flex items-center gap-1 border border-[#9CBF9B]/30">
                            <span class="material-symbols-outlined text-[14px]">verified</span> Verified
                        </span>
                    @endif
                    <span class="px-3 py-1 bg-slate-100 text-slate-600 text-xs font-bold rounded-full border border-slate-200">
                        Member since {{ auth()->user()->created_at->format('Y') }}
                    </span>
                </div>
            </div>

            <!-- Profile Completion -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                <div class="flex items-center justify-between mb-3">
                    <h4 class="font-bold text-[#111418] text-sm">Profile Completion</h4>
                    <span class="text-[#E3655B] font-bold text-sm">{{ $this->getProfileCompletion() }}%</span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-2 mb-4">
                    <div class="bg-[#E3655B] h-2 rounded-full transition-all" style="width: {{ $this->getProfileCompletion() }}%"></div>
                </div>
                <ul class="space-y-3">
                    <li class="flex items-center gap-3 text-sm {{ auth()->user()->email_verified_at ? 'text-slate-500 line-through decoration-slate-400' : 'text-[#111418] font-medium' }}">
                        <span class="material-symbols-outlined text-[18px] {{ auth()->user()->email_verified_at ? 'text-[#9CBF9B]' : 'text-slate-300' }}">
                            {{ auth()->user()->email_verified_at ? 'check_circle' : 'radio_button_unchecked' }}
                        </span>
                        Verify Email
                    </li>
                    <li class="flex items-center gap-3 text-sm {{ auth()->user()->phone ? 'text-slate-500 line-through decoration-slate-400' : 'text-[#111418] font-medium' }}">
                        <span class="material-symbols-outlined text-[18px] {{ auth()->user()->phone ? 'text-[#9CBF9B]' : 'text-slate-300' }}">
                            {{ auth()->user()->phone ? 'check_circle' : 'radio_button_unchecked' }}
                        </span>
                        Add Phone Number
                    </li>
                    <li class="flex items-center gap-3 text-sm {{ auth()->user()->bookings()->exists() ? 'text-slate-500 line-through decoration-slate-400' : 'text-[#111418] font-medium' }}">
                        <span class="material-symbols-outlined text-[18px] {{ auth()->user()->bookings()->exists() ? 'text-[#9CBF9B]' : 'text-slate-300' }}">
                            {{ auth()->user()->bookings()->exists() ? 'check_circle' : 'radio_button_unchecked' }}
                        </span>
                        Complete First Booking
                    </li>
                </ul>
            </div>

            <!-- Need Help -->
            <div class="bg-[#CFD186]/20 rounded-xl border border-[#CFD186]/30 p-6 flex flex-col gap-3">
                <h4 class="font-bold text-[#111418] flex items-center gap-2">
                    <span class="material-symbols-outlined text-[#E3655B]">support_agent</span>
                    Need Help?
                </h4>
                <p class="text-sm text-slate-600">Our support team is available 24/7 to assist you with profile updates.</p>
                <button class="text-[#E3655B] text-sm font-bold hover:underline self-start">Contact Support</button>
            </div>
        </div>

        <!-- Right Column: Forms -->
        <div class="lg:col-span-2 flex flex-col gap-8">
            <!-- Flash Message -->
            @if(session('success'))
                <div class="p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 flex items-center gap-3">
                    <span class="material-symbols-outlined">check_circle</span>
                    {{ session('success') }}
                </div>
            @endif

            <!-- Personal Information -->
            <section class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-white">
                    <h3 class="font-bold text-lg text-[#111418]">Personal Information</h3>
                </div>
                <div class="p-6">
                    <form wire:submit="saveProfile" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide" for="firstName">First Name</label>
                            <input wire:model="firstName" class="w-full py-3 rounded-lg border-slate-300 text-slate-900 focus:ring-[#E3655B] focus:border-[#E3655B] bg-slate-50" id="firstName" type="text">
                            @error('firstName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide" for="lastName">Last Name</label>
                            <input wire:model="lastName" class="w-full py-3 rounded-lg border-slate-300 text-slate-900 focus:ring-[#E3655B] focus:border-[#E3655B] bg-slate-50" id="lastName" type="text">
                            @error('lastName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div class="space-y-1.5 md:col-span-2">
                            <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide" for="email">Email Address</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="material-symbols-outlined text-slate-400 text-[20px]">mail</span>
                                </span>
                                <input wire:model="email" class="w-full py-3 pl-10 rounded-lg border-slate-300 text-slate-900 focus:ring-[#E3655B] focus:border-[#E3655B] bg-slate-50" id="email" type="email" disabled>
                            </div>
                            <p class="text-xs text-slate-400">Email cannot be changed</p>
                        </div>
                        <div class="space-y-1.5 md:col-span-2">
                            <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide" for="phone">Phone Number</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="material-symbols-outlined text-slate-400 text-[20px]">call</span>
                                </span>
                                <input wire:model="phone" class="w-full py-3 pl-10 rounded-lg border-slate-300 text-slate-900 focus:ring-[#E3655B] focus:border-[#E3655B] bg-slate-50" id="phone" type="tel" placeholder="+234 800 123 4567">
                            </div>
                            @error('phone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div class="md:col-span-2 flex justify-end mt-2">
                            <button type="submit" class="bg-[#111418] text-white font-bold py-2.5 px-6 rounded-lg hover:bg-slate-800 transition-colors shadow-sm">
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </section>

            <!-- Communication Preferences -->
            <section class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-100 bg-white">
                    <h3 class="font-bold text-lg text-[#111418]">Communication Preferences</h3>
                </div>
                <div class="p-6 flex flex-col gap-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-sm font-bold text-[#111418]">Booking Notifications</h4>
                            <p class="text-xs text-slate-500">Receive updates about your car rental status via email.</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" wire:model.live="bookingNotifications" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-[#9CBF9B]/50 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#9CBF9B]"></div>
                        </label>
                    </div>
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-sm font-bold text-[#111418]">Marketing & Offers</h4>
                            <p class="text-xs text-slate-500">Get notified about discounts and special promos.</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" wire:model.live="marketingOffers" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-[#9CBF9B]/50 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#9CBF9B]"></div>
                        </label>
                    </div>
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-sm font-bold text-[#111418]">SMS Alerts</h4>
                            <p class="text-xs text-slate-500">Receive urgent updates on your phone.</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" wire:model.live="smsAlerts" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-[#9CBF9B]/50 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#9CBF9B]"></div>
                        </label>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
