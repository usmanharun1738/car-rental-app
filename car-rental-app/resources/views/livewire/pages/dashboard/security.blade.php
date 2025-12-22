<?php

use Livewire\Volt\Component;
use Livewire\Attributes\{Layout, Title, Validate};
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

new #[Layout('components.layouts.dashboard')] #[Title('Security Settings - CARTAR')] class extends Component
{
    #[Validate('required|current_password')]
    public string $currentPassword = '';

    #[Validate('required|min:8|confirmed')]
    public string $newPassword = '';

    public string $newPassword_confirmation = '';

    public bool $twoFactorEnabled = false;

    public function updatePassword(): void
    {
        $this->validate([
            'currentPassword' => ['required', 'current_password'],
            'newPassword' => ['required', Password::defaults(), 'confirmed'],
        ]);

        auth()->user()->update([
            'password' => Hash::make($this->newPassword),
        ]);

        $this->reset(['currentPassword', 'newPassword', 'newPassword_confirmation']);
        session()->flash('success', 'Password updated successfully.');
    }

    public function getRecentActivity(): array
    {
        return [
            [
                'event' => 'Successful Login',
                'date' => now()->format('M d, Y \a\t h:i A'),
                'detail' => 'Device: ' . request()->userAgent(),
                'color' => 'bg-[#9CBF9B]',
            ],
            [
                'event' => 'Profile Updated',
                'date' => auth()->user()->updated_at->format('M d, Y \a\t h:i A'),
                'detail' => 'Via Profile Settings',
                'color' => 'bg-slate-300',
            ],
            [
                'event' => 'Account Created',
                'date' => auth()->user()->created_at->format('M d, Y \a\t h:i A'),
                'detail' => 'Welcome to CARTAR!',
                'color' => 'bg-slate-300',
            ],
        ];
    }
}; ?>

<x-slot:breadcrumb>Security</x-slot:breadcrumb>

<div class="max-w-5xl mx-auto flex flex-col gap-8">
    <!-- Header -->
    <div>
        <h2 class="text-2xl font-bold text-[#111418]">Security Settings</h2>
        <p class="text-slate-500">Manage your password, login sessions, and account security preferences.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column: Password & 2FA -->
        <div class="lg:col-span-2 flex flex-col gap-8">
            <!-- Flash Message -->
            @if(session('success'))
                <div class="p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 flex items-center gap-3">
                    <span class="material-symbols-outlined">check_circle</span>
                    {{ session('success') }}
                </div>
            @endif

            <!-- Change Password -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center gap-3">
                    <div class="bg-red-50 p-2 rounded-lg">
                        <span class="material-symbols-outlined text-[#E3655B]">lock_reset</span>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg text-[#111418]">Change Password</h3>
                        <p class="text-xs text-slate-500">Update your password to keep your account secure</p>
                    </div>
                </div>
                <div class="p-6">
                    <form wire:submit="updatePassword" class="flex flex-col gap-5">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Current Password</label>
                            <input wire:model="currentPassword" type="password" class="w-full py-3 rounded-lg border-slate-300 shadow-sm focus:border-[#E3655B] focus:ring focus:ring-[#E3655B]/20" placeholder="••••••••">
                            @error('currentPassword') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">New Password</label>
                                <input wire:model="newPassword" type="password" class="w-full py-3 rounded-lg border-slate-300 shadow-sm focus:border-[#E3655B] focus:ring focus:ring-[#E3655B]/20" placeholder="••••••••">
                                @error('newPassword') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Confirm New Password</label>
                                <input wire:model="newPassword_confirmation" type="password" class="w-full py-3 rounded-lg border-slate-300 shadow-sm focus:border-[#E3655B] focus:ring focus:ring-[#E3655B]/20" placeholder="••••••••">
                            </div>
                        </div>
                        <div class="pt-2 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                            <p class="text-xs text-slate-500 max-w-sm">
                                Make sure your password is at least 8 characters long, includes a special character and a number.
                            </p>
                            <button type="submit" class="bg-[#E3655B] hover:bg-[#d6554b] text-white px-5 py-2.5 rounded-lg text-sm font-bold shadow-sm transition-colors w-full md:w-auto">
                                Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Two-Factor Authentication -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center gap-3">
                    <div class="bg-[#CFD186]/20 p-2 rounded-lg">
                        <span class="material-symbols-outlined text-slate-700">phonelink_lock</span>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg text-[#111418]">Two-Factor Authentication</h3>
                        <p class="text-xs text-slate-500">Add an extra layer of security</p>
                    </div>
                </div>
                <div class="p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="material-symbols-outlined text-slate-400">smartphone</span>
                            <h4 class="font-bold text-slate-700 text-sm">Text Message (SMS)</h4>
                        </div>
                        <p class="text-sm text-slate-500 mb-2">Receive a security code via SMS on your registered phone number.</p>
                        <p class="text-xs text-orange-600 bg-orange-50 inline-block px-2 py-1 rounded border border-orange-100">Coming Soon - Enhanced Security</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-sm font-bold text-slate-400">Off</span>
                        <button disabled class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-not-allowed rounded-full border-2 border-transparent bg-slate-200 transition-colors duration-200 ease-in-out opacity-50">
                            <span class="translate-x-0 pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Active Sessions -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="font-bold text-lg text-[#111418]">Active Login Sessions</h3>
                    <button disabled class="text-sm text-slate-400 font-bold cursor-not-allowed" title="Coming Soon">Log Out All Devices</button>
                </div>
                <div class="divide-y divide-slate-100">
                    <div class="p-5 flex items-start gap-4 hover:bg-slate-50 transition-colors">
                        <div class="size-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 flex-shrink-0">
                            <span class="material-symbols-outlined">laptop_mac</span>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center gap-2">
                                <p class="text-sm font-bold text-[#111418]">Current Device</p>
                                <span class="bg-[#9CBF9B]/20 text-[#2C5E2E] text-[10px] font-bold px-1.5 py-0.5 rounded border border-[#9CBF9B]/30">Active Now</span>
                            </div>
                            <p class="text-xs text-slate-500 mt-0.5">{{ request()->ip() }} • {{ request()->header('User-Agent') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Activity & Support -->
        <div class="lg:col-span-1 flex flex-col gap-8">
            <!-- Recent Activity -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden h-fit">
                <div class="p-5 border-b border-slate-100 bg-white">
                    <h3 class="font-bold text-lg text-[#111418]">Recent Activity</h3>
                    <p class="text-xs text-slate-500">Latest security events on your account</p>
                </div>
                <div class="relative">
                    <div class="absolute top-6 bottom-6 left-6 w-px bg-slate-200"></div>
                    <div class="flex flex-col">
                        @foreach($this->getRecentActivity() as $activity)
                            <div class="relative pl-12 pr-6 py-4 hover:bg-slate-50 transition-colors">
                                <div class="absolute left-6 top-6 -ml-1.5 w-3 h-3 {{ $activity['color'] }} rounded-full border-2 border-white shadow-sm z-10"></div>
                                <p class="text-sm font-bold text-[#111418]">{{ $activity['event'] }}</p>
                                <p class="text-xs text-slate-500 mt-0.5">{{ $activity['date'] }}</p>
                                <p class="text-xs text-slate-400 mt-1 truncate">{{ Str::limit($activity['detail'], 30) }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Support Card -->
            <div class="bg-[#101922] rounded-xl p-6 text-white relative overflow-hidden shadow-lg">
                <div class="absolute right-0 bottom-0 w-32 h-32 bg-[#E3655B] rounded-full opacity-10 blur-2xl translate-x-10 translate-y-10"></div>
                <div class="relative z-10">
                    <div class="size-10 bg-white/10 rounded-lg flex items-center justify-center mb-4 backdrop-blur-sm">
                        <span class="material-symbols-outlined text-[#CFD186]">support_agent</span>
                    </div>
                    <h4 class="font-bold text-lg mb-2">Need help with security?</h4>
                    <p class="text-slate-400 text-sm mb-4">If you notice suspicious activity on your account, please contact our support team immediately.</p>
                    <a href="tel:+2348001234567" class="w-full bg-white text-[#111418] py-2.5 rounded-lg text-sm font-bold hover:bg-slate-100 transition-colors block text-center">
                        Contact Support
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
