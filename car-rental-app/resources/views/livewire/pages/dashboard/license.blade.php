<?php

use App\Enums\LicenseStatus;
use App\Models\DriverLicense;
use Livewire\Volt\Component;
use Livewire\Attributes\{Layout, Title, Validate};
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

new #[Layout('components.layouts.dashboard')] #[Title('Driver\'s License - CARTAR')] class extends Component
{
    use WithFileUploads;

    #[Validate('required|string|max:50')]
    public string $licenseNumber = '';

    #[Validate('required|string|max:255')]
    public string $fullName = '';

    #[Validate('required|date')]
    public string $dateOfBirth = '';

    #[Validate('required|string|max:5')]
    public string $licenseClass = 'E';

    #[Validate('required|in:M,F')]
    public string $sex = 'M';

    #[Validate('required|date')]
    public string $issueDate = '';

    #[Validate('required|date|after:issue_date')]
    public string $expiryDate = '';

    #[Validate('required|string|max:100')]
    public string $stateOfIssue = '';

    #[Validate('nullable|image|max:5120')]
    public $frontImage = null;

    #[Validate('nullable|image|max:5120')]
    public $backImage = null;

    public ?DriverLicense $license = null;

    public function mount(): void
    {
        $this->license = auth()->user()->driverLicense;
        
        if ($this->license) {
            $this->licenseNumber = $this->license->license_number;
            $this->fullName = $this->license->full_name;
            $this->dateOfBirth = $this->license->date_of_birth->format('Y-m-d');
            $this->licenseClass = $this->license->license_class;
            $this->sex = $this->license->sex;
            $this->issueDate = $this->license->issue_date->format('Y-m-d');
            $this->expiryDate = $this->license->expiry_date->format('Y-m-d');
            $this->stateOfIssue = $this->license->state_of_issue;
        } else {
            $user = auth()->user();
            $this->fullName = $user->name;
        }
    }

    public function saveLicense(): void
    {
        $this->validate();

        $data = [
            'user_id' => auth()->id(),
            'license_number' => $this->licenseNumber,
            'full_name' => $this->fullName,
            'date_of_birth' => $this->dateOfBirth,
            'license_class' => $this->licenseClass,
            'sex' => $this->sex,
            'issue_date' => $this->issueDate,
            'expiry_date' => $this->expiryDate,
            'state_of_issue' => $this->stateOfIssue,
            'issuing_authority' => 'FRSC',
            'status' => LicenseStatus::PENDING,
        ];

        // Handle front image upload
        if ($this->frontImage) {
            $frontPath = $this->frontImage->store('licenses', 'public');
            $data['front_image_path'] = $frontPath;
        }

        // Handle back image upload
        if ($this->backImage) {
            $backPath = $this->backImage->store('licenses', 'public');
            $data['back_image_path'] = $backPath;
        }

        if ($this->license) {
            // Delete old images if new ones are uploaded
            if ($this->frontImage && $this->license->front_image_path) {
                Storage::disk('public')->delete($this->license->front_image_path);
            }
            if ($this->backImage && $this->license->back_image_path) {
                Storage::disk('public')->delete($this->license->back_image_path);
            }
            $this->license->update($data);
        } else {
            $this->license = DriverLicense::create($data);
        }

        session()->flash('success', 'License information submitted for verification.');
        $this->redirect(route('dashboard.license'), navigate: true);
    }

    public function getStatusBadge(): array
    {
        if (!$this->license) {
            return ['bg' => 'bg-gray-100', 'text' => 'text-gray-600', 'label' => 'Not Submitted'];
        }

        return match($this->license->status) {
            LicenseStatus::PENDING => ['bg' => 'bg-amber-100', 'text' => 'text-amber-700', 'label' => 'Pending Review'],
            LicenseStatus::VERIFIED => ['bg' => 'bg-green-100', 'text' => 'text-green-700', 'label' => 'Verified'],
            LicenseStatus::REJECTED => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'label' => 'Rejected'],
            LicenseStatus::EXPIRED => ['bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'label' => 'Expired'],
        };
    }
}; ?>

<x-slot:breadcrumb>Driver's License</x-slot:breadcrumb>

<div class="max-w-5xl mx-auto flex flex-col gap-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
        <div class="flex flex-col gap-1">
            <h2 class="text-2xl font-bold text-[#111418]">Driver's License & Verification</h2>
            <p class="text-slate-500">Manage your driving credentials and view verification status.</p>
        </div>
        @php $badge = $this->getStatusBadge(); @endphp
        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full {{ $badge['bg'] }} border {{ str_replace('bg-', 'border-', $badge['bg']) }} {{ $badge['text'] }} font-medium text-sm">
            <span class="material-symbols-outlined text-base">{{ $license && $license->status === \App\Enums\LicenseStatus::VERIFIED ? 'verified' : 'pending' }}</span>
            {{ $badge['label'] }}
        </div>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 flex items-center gap-3">
            <span class="material-symbols-outlined">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    @if($license && $license->status === LicenseStatus::REJECTED)
        <div class="p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 flex items-start gap-3">
            <span class="material-symbols-outlined">error</span>
            <div>
                <p class="font-bold">Your license was rejected</p>
                <p class="text-sm mt-1">{{ $license->rejection_reason }}</p>
                <p class="text-sm mt-2">Please update your information and resubmit.</p>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column: License Card & Form -->
        <div class="lg:col-span-2 flex flex-col gap-6">
            <!-- License Card Display -->
            @if($license && $license->status === LicenseStatus::VERIFIED)
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="p-5 border-b border-slate-100 flex justify-between items-center">
                        <h3 class="font-bold text-lg text-[#111418]">Current License</h3>
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-[#9CBF9B]/20 text-[#2C5E2E]">Verified</span>
                    </div>
                    <div class="p-6 md:p-8 bg-slate-50/50">
                        <div class="relative w-full max-w-md mx-auto aspect-[1.586] bg-gradient-to-br from-[#101922] to-[#2C3E50] rounded-2xl shadow-xl overflow-hidden text-white p-6 flex flex-col justify-between group">
                            <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 20px 20px;"></div>
                            <div class="absolute -right-12 -top-12 w-48 h-48 bg-[#9CBF9B] rounded-full blur-3xl opacity-20"></div>
                            <div class="absolute -left-12 -bottom-12 w-48 h-48 bg-[#E3655B] rounded-full blur-3xl opacity-20"></div>
                            
                            <div class="relative z-10 flex justify-between items-start">
                                <div class="flex items-center gap-2">
                                    <span class="text-2xl">🇳🇬</span>
                                    <div>
                                        <p class="text-[10px] uppercase tracking-wider text-slate-300 font-bold">Federal Republic of Nigeria</p>
                                        <p class="text-xs font-bold text-white">DRIVER'S LICENSE</p>
                                    </div>
                                </div>
                                <span class="material-symbols-outlined text-[#CFD186] text-3xl">local_police</span>
                            </div>

                            <div class="relative z-10 flex gap-4 mt-4">
                                <div class="w-24 h-24 bg-slate-200 rounded-lg overflow-hidden border-2 border-white/20 shadow-inner flex-shrink-0 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-slate-400 text-4xl">person</span>
                                </div>
                                <div class="flex-1 space-y-3">
                                    <div>
                                        <p class="text-[10px] text-slate-400 uppercase">License Number</p>
                                        <p class="font-mono text-base md:text-lg tracking-widest text-[#CFD186] font-bold">{{ $license->license_number }}</p>
                                    </div>
                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <p class="text-[10px] text-slate-400 uppercase">Class</p>
                                            <p class="text-xs font-bold">{{ $license->license_class }}</p>
                                        </div>
                                        <div>
                                            <p class="text-[10px] text-slate-400 uppercase">Sex</p>
                                            <p class="text-xs font-bold">{{ $license->sex }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="relative z-10 flex justify-between items-end mt-4">
                                <div>
                                    <p class="text-[10px] text-slate-400 uppercase">Issue Date</p>
                                    <p class="text-xs font-bold">{{ $license->issue_date->format('d M Y') }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-[10px] text-slate-400 uppercase">Expires</p>
                                    <p class="text-sm font-bold {{ $license->isExpired() ? 'text-[#E3655B]' : 'text-white' }}">{{ $license->expiry_date->format('d M Y') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">Full Name</label>
                            <div class="p-3 bg-slate-50 border border-slate-200 rounded-lg text-slate-800 font-medium">
                                {{ $license->full_name }}
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">Date of Birth</label>
                            <div class="p-3 bg-slate-50 border border-slate-200 rounded-lg text-slate-800 font-medium">
                                {{ $license->date_of_birth->format('d M Y') }}
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">Issuing Authority</label>
                            <div class="p-3 bg-slate-50 border border-slate-200 rounded-lg text-slate-800 font-medium">
                                {{ $license->issuing_authority }}
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">State of Issue</label>
                            <div class="p-3 bg-slate-50 border border-slate-200 rounded-lg text-slate-800 font-medium">
                                {{ $license->state_of_issue }}
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- License Form -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="p-5 border-b border-slate-100">
                        <h3 class="font-bold text-lg text-[#111418]">{{ $license ? 'Update License Information' : 'Submit License Information' }}</h3>
                    </div>
                    <div class="p-6">
                        <form wire:submit="saveLicense" class="flex flex-col gap-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-1.5">
                                    <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide">License Number *</label>
                                    <input wire:model="licenseNumber" type="text" class="w-full py-3 px-4 rounded-lg border border-slate-200 focus:outline-none focus:ring-1 focus:ring-[#E3655B] focus:border-[#E3655B] bg-slate-50" placeholder="e.g., ABJ123456789">
                                    @error('licenseNumber') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div class="space-y-1.5">
                                    <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Full Name (as on license) *</label>
                                    <input wire:model="fullName" type="text" class="w-full py-3 px-4 rounded-lg border border-slate-200 focus:outline-none focus:ring-1 focus:ring-[#E3655B] focus:border-[#E3655B] bg-slate-50">
                                    @error('fullName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                                <div class="space-y-1.5">
                                    <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Date of Birth *</label>
                                    <input wire:model="dateOfBirth" type="date" class="w-full py-3 px-4 rounded-lg border border-slate-200 focus:outline-none focus:ring-1 focus:ring-[#E3655B] focus:border-[#E3655B] bg-slate-50">
                                    @error('dateOfBirth') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div class="space-y-1.5">
                                    <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide">License Class *</label>
                                    <select wire:model="licenseClass" class="w-full py-3 px-4 rounded-lg border border-slate-200 focus:outline-none focus:ring-1 focus:ring-[#E3655B] focus:border-[#E3655B] bg-slate-50">
                                        <option value="A">A - Motorcycle</option>
                                        <option value="B">B - Light Vehicle</option>
                                        <option value="C">C - Light Commercial</option>
                                        <option value="D">D - Truck</option>
                                        <option value="E">E - Car</option>
                                        <option value="F">F - Heavy Vehicle</option>
                                    </select>
                                </div>
                                <div class="space-y-1.5">
                                    <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Sex *</label>
                                    <select wire:model="sex" class="w-full py-3 px-4 rounded-lg border border-slate-200 focus:outline-none focus:ring-1 focus:ring-[#E3655B] focus:border-[#E3655B] bg-slate-50">
                                        <option value="M">Male</option>
                                        <option value="F">Female</option>
                                    </select>
                                </div>
                                <div class="space-y-1.5">
                                    <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide">State of Issue *</label>
                                    <select wire:model="stateOfIssue" class="w-full py-3 px-4 rounded-lg border border-slate-200 focus:outline-none focus:ring-1 focus:ring-[#E3655B] focus:border-[#E3655B] bg-slate-50">
                                        <option value="">Select State</option>
                                        <option value="FCT Abuja">FCT Abuja</option>
                                        <option value="Lagos">Lagos</option>
                                        <option value="Kano">Kano</option>
                                        <option value="Rivers">Rivers</option>
                                        <option value="Kaduna">Kaduna</option>
                                        <option value="Oyo">Oyo</option>
                                        <option value="Ogun">Ogun</option>
                                        <option value="Delta">Delta</option>
                                        <option value="Enugu">Enugu</option>
                                        <option value="Anambra">Anambra</option>
                                    </select>
                                    @error('stateOfIssue') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-1.5">
                                    <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Issue Date *</label>
                                    <input wire:model="issueDate" type="date" class="w-full py-3 px-4 rounded-lg border border-slate-200 focus:outline-none focus:ring-1 focus:ring-[#E3655B] focus:border-[#E3655B] bg-slate-50">
                                    @error('issueDate') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div class="space-y-1.5">
                                    <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Expiry Date *</label>
                                    <input wire:model="expiryDate" type="date" class="w-full py-3 px-4 rounded-lg border border-slate-200 focus:outline-none focus:ring-1 focus:ring-[#E3655B] focus:border-[#E3655B] bg-slate-50">
                                    @error('expiryDate') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="border-t border-slate-100 pt-6">
                                <h4 class="font-bold text-[#111418] mb-4">Upload License Photos</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="space-y-2">
                                        <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Front of License</label>
                                        <div class="border-2 border-dashed border-slate-300 rounded-xl p-6 flex flex-col items-center justify-center text-center hover:border-[#E3655B] hover:bg-red-50/10 transition-colors group cursor-pointer relative">
                                            <input wire:model="frontImage" type="file" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                            @if($frontImage)
                                                <img src="{{ $frontImage->temporaryUrl() }}" class="w-full h-32 object-cover rounded-lg mb-2">
                                                <p class="text-xs text-green-600 font-medium">Image selected</p>
                                            @elseif($license && $license->front_image_url)
                                                <img src="{{ $license->front_image_url }}" class="w-full h-32 object-cover rounded-lg mb-2">
                                                <p class="text-xs text-slate-500">Click to replace</p>
                                            @else
                                                <div class="size-12 rounded-full bg-slate-100 flex items-center justify-center mb-3 group-hover:bg-[#E3655B]/10 transition-colors">
                                                    <span class="material-symbols-outlined text-slate-400 group-hover:text-[#E3655B]">cloud_upload</span>
                                                </div>
                                                <p class="text-sm font-bold text-[#111418]">Click to upload</p>
                                                <p class="text-xs text-slate-500 mt-1">PNG, JPG (max. 5MB)</p>
                                            @endif
                                        </div>
                                        @error('frontImage') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Back of License</label>
                                        <div class="border-2 border-dashed border-slate-300 rounded-xl p-6 flex flex-col items-center justify-center text-center hover:border-[#E3655B] hover:bg-red-50/10 transition-colors group cursor-pointer relative">
                                            <input wire:model="backImage" type="file" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                            @if($backImage)
                                                <img src="{{ $backImage->temporaryUrl() }}" class="w-full h-32 object-cover rounded-lg mb-2">
                                                <p class="text-xs text-green-600 font-medium">Image selected</p>
                                            @elseif($license && $license->back_image_url)
                                                <img src="{{ $license->back_image_url }}" class="w-full h-32 object-cover rounded-lg mb-2">
                                                <p class="text-xs text-slate-500">Click to replace</p>
                                            @else
                                                <div class="size-12 rounded-full bg-slate-100 flex items-center justify-center mb-3 group-hover:bg-[#E3655B]/10 transition-colors">
                                                    <span class="material-symbols-outlined text-slate-400 group-hover:text-[#E3655B]">cloud_upload</span>
                                                </div>
                                                <p class="text-sm font-bold text-[#111418]">Click to upload</p>
                                                <p class="text-xs text-slate-500 mt-1">PNG, JPG (max. 5MB)</p>
                                            @endif
                                        </div>
                                        @error('backImage') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end">
                                <button type="submit" class="bg-[#111418] text-white font-bold py-3 px-8 rounded-lg hover:bg-slate-800 transition-colors">
                                    Submit for Verification
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        </div>

        <!-- Right Column: Info -->
        <div class="lg:col-span-1 flex flex-col gap-6">
            <!-- Requirements -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                <h4 class="font-bold text-[#111418] mb-4">Document Requirements</h4>
                <div class="flex flex-col gap-3">
                    <div class="flex items-center gap-2 text-xs text-slate-500">
                        <span class="material-symbols-outlined text-sm text-[#9CBF9B]">check_circle</span>
                        Document must be valid
                    </div>
                    <div class="flex items-center gap-2 text-xs text-slate-500">
                        <span class="material-symbols-outlined text-sm text-[#9CBF9B]">check_circle</span>
                        All 4 corners must be visible
                    </div>
                    <div class="flex items-center gap-2 text-xs text-slate-500">
                        <span class="material-symbols-outlined text-sm text-[#9CBF9B]">check_circle</span>
                        Text must be clear and readable
                    </div>
                    <div class="flex items-center gap-2 text-xs text-slate-500">
                        <span class="material-symbols-outlined text-sm text-[#9CBF9B]">check_circle</span>
                        No blur or glare
                    </div>
                </div>
            </div>

            <!-- Verification Info -->
            <div class="bg-[#E3655B]/5 border border-[#E3655B]/20 rounded-xl p-5">
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-[#E3655B] mt-0.5">info</span>
                    <div>
                        <h4 class="text-sm font-bold text-[#111418]">Verification Process</h4>
                        <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                            Verification usually takes 24-48 hours. You will receive an email notification once your document has been approved. You cannot rent a car while your license is under review if your previous one has expired.
                        </p>
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
                    <h4 class="font-bold text-lg mb-2">Having trouble?</h4>
                    <p class="text-slate-400 text-sm mb-4">Contact our support team for help with license verification.</p>
                    <a href="tel:+2348001234567" class="w-full bg-white text-[#111418] py-2.5 rounded-lg text-sm font-bold hover:bg-slate-100 transition-colors block text-center">
                        Contact Support
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
