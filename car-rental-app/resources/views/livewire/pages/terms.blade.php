<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

new #[Layout('components.layouts.guest')] #[Title('Terms of Service - CARTAR')] class extends Component {
    public function with(): array
    {
        return [
            'lastUpdated' => 'December 27, 2025',
        ];
    }
}; ?>

<div class="min-h-screen bg-[#f6f7f8]">
    <div class="w-full max-w-[1280px] mx-auto px-4 md:px-10 py-8 md:py-12">
        <!-- Back Link -->
        <a class="inline-flex items-center gap-2 text-[#617589] text-sm font-medium leading-normal hover:text-[#FF6B35] mb-6 transition-colors" href="{{ route('home') }}" wire:navigate>
            <span class="material-symbols-outlined text-sm">arrow_back</span>
            Back to Home
        </a>

        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 border-b border-gray-200 pb-8 mb-8">
            <div class="max-w-2xl">
                <h1 class="text-gray-900 text-4xl md:text-5xl font-black leading-tight tracking-[-0.033em] mb-3 relative inline-block">
                    Terms of Service
                    <span class="absolute bottom-1 left-0 w-full h-3 bg-[#CFD186]/40 -z-10 rounded-sm"></span>
                </h1>
                <p class="text-[#617589] text-base font-normal leading-normal">
                    Please read these terms carefully before using our services. Last updated: <span class="font-medium text-gray-900">{{ $lastUpdated }}</span>
                </p>
            </div>
            <button class="hidden md:flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-200 hover:bg-gray-50 transition-colors text-sm font-medium">
                <span class="material-symbols-outlined text-lg">download</span>
                Download PDF
            </button>
        </div>

        <div class="flex flex-col lg:flex-row gap-12 relative">
            <!-- Sidebar Navigation (Sticky) -->
            <aside class="hidden lg:block w-64 shrink-0">
                <div class="sticky top-28 flex flex-col gap-1">
                    <p class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-2 px-3">Contents</p>
                    <a class="group flex items-center justify-between rounded-lg px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 hover:text-[#FF6B35] transition-all" href="#section-1">
                        <span>1. Acceptance</span>
                        <span class="material-symbols-outlined text-[16px] opacity-0 group-hover:opacity-100 transition-opacity">chevron_right</span>
                    </a>
                    <a class="group flex items-center justify-between rounded-lg px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 hover:text-[#FF6B35] transition-all" href="#section-2">
                        <span>2. Eligibility</span>
                        <span class="material-symbols-outlined text-[16px] opacity-0 group-hover:opacity-100 transition-opacity">chevron_right</span>
                    </a>
                    <a class="group flex items-center justify-between rounded-lg px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 hover:text-[#FF6B35] transition-all" href="#section-3">
                        <span>3. Registration</span>
                        <span class="material-symbols-outlined text-[16px] opacity-0 group-hover:opacity-100 transition-opacity">chevron_right</span>
                    </a>
                    <a class="group flex items-center justify-between rounded-lg px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 hover:text-[#FF6B35] transition-all" href="#section-4">
                        <span>4. Booking</span>
                        <span class="material-symbols-outlined text-[16px] opacity-0 group-hover:opacity-100 transition-opacity">chevron_right</span>
                    </a>
                    <a class="group flex items-center justify-between rounded-lg px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 hover:text-[#FF6B35] transition-all" href="#section-5">
                        <span>5. Fees & Payments</span>
                        <span class="material-symbols-outlined text-[16px] opacity-0 group-hover:opacity-100 transition-opacity">chevron_right</span>
                    </a>
                    <a class="group flex items-center justify-between rounded-lg px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 hover:text-[#FF6B35] transition-all" href="#section-6">
                        <span>6. Vehicle Use</span>
                        <span class="material-symbols-outlined text-[16px] opacity-0 group-hover:opacity-100 transition-opacity">chevron_right</span>
                    </a>
                    <a class="group flex items-center justify-between rounded-lg px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 hover:text-[#FF6B35] transition-all" href="#section-7">
                        <span>7. Cancellations</span>
                        <span class="material-symbols-outlined text-[16px] opacity-0 group-hover:opacity-100 transition-opacity">chevron_right</span>
                    </a>
                    <a class="group flex items-center justify-between rounded-lg px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 hover:text-[#FF6B35] transition-all" href="#section-8">
                        <span>8. Insurance</span>
                        <span class="material-symbols-outlined text-[16px] opacity-0 group-hover:opacity-100 transition-opacity">chevron_right</span>
                    </a>
                    <a class="group flex items-center justify-between rounded-lg px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 hover:text-[#FF6B35] transition-all" href="#section-9">
                        <span>9. Force Majeure</span>
                        <span class="material-symbols-outlined text-[16px] opacity-0 group-hover:opacity-100 transition-opacity">chevron_right</span>
                    </a>
                    <a class="group flex items-center justify-between rounded-lg px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 hover:text-[#FF6B35] transition-all" href="#section-10">
                        <span>10. Geo Restrictions</span>
                        <span class="material-symbols-outlined text-[16px] opacity-0 group-hover:opacity-100 transition-opacity">chevron_right</span>
                    </a>
                    <a class="group flex items-center justify-between rounded-lg px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 hover:text-[#FF6B35] transition-all" href="#section-11">
                        <span>11. Termination</span>
                        <span class="material-symbols-outlined text-[16px] opacity-0 group-hover:opacity-100 transition-opacity">chevron_right</span>
                    </a>
                    <a class="group flex items-center justify-between rounded-lg px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 hover:text-[#FF6B35] transition-all" href="#section-12">
                        <span>12. Governing Law</span>
                        <span class="material-symbols-outlined text-[16px] opacity-0 group-hover:opacity-100 transition-opacity">chevron_right</span>
                    </a>
                    <a class="mt-4 flex items-center gap-2 rounded-lg bg-[#9CBF9B]/10 px-3 py-2 text-sm font-bold text-green-700 hover:bg-[#9CBF9B]/20 transition-colors" href="#section-contact">
                        <span class="material-symbols-outlined text-lg">support_agent</span>
                        Need Help?
                    </a>
                </div>
            </aside>

            <!-- Main Content -->
            <div class="flex-1 max-w-[800px]">
                <div class="prose prose-lg max-w-none text-gray-900">
                    <p class="text-lg leading-relaxed mb-8">
                        Welcome to CARTAR. By accessing our website and using our vehicle rental services, you agree to be bound by these Terms of Service. These terms constitute a legally binding agreement between you ("User" or "Renter") and CARTAR ("Company", "we", "us"). If you do not agree to these terms, please do not use our services.
                    </p>

                    <!-- Clause 1 -->
                    <div class="mb-10 scroll-mt-28 group" id="section-1">
                        <h2 class="text-2xl font-bold mb-4 flex items-center gap-3">
                            <span class="flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 text-sm font-bold text-gray-500 group-hover:bg-[#FF6B35] group-hover:text-white transition-colors">1</span>
                            Acceptance of Terms
                        </h2>
                        <p class="text-gray-600 leading-relaxed pl-11">
                            By creating an account, making a reservation, or using any part of the CARTAR platform, you acknowledge that you have read, understood, and agree to comply with these Terms of Service and our Privacy Policy. We reserve the right to modify these terms at any time. Continued use of the service following any changes signifies your acceptance of the new terms.
                        </p>
                    </div>

                    <!-- Clause 2 -->
                    <div class="mb-10 scroll-mt-28 group" id="section-2">
                        <h2 class="text-2xl font-bold mb-4 flex items-center gap-3">
                            <span class="flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 text-sm font-bold text-gray-500 group-hover:bg-[#FF6B35] group-hover:text-white transition-colors">2</span>
                            Eligibility
                        </h2>
                        <div class="pl-11 space-y-3 text-gray-600">
                            <p class="leading-relaxed">To use CARTAR services, you must meet the following criteria:</p>
                            <ul class="list-disc pl-5 space-y-2 marker:text-[#9CBF9B]">
                                <li>Be at least 21 years of age (drivers under 25 may be subject to a "Young Driver" surcharge).</li>
                                <li>Possess a valid driver's license issued in Nigeria or a valid International Driving Permit (IDP).</li>
                                <li>Have a valid credit or debit card for payment and security deposit purposes.</li>
                                <li>Have a clean driving record with no major violations in the last 3 years.</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Clause 3 -->
                    <div class="mb-10 scroll-mt-28 group" id="section-3">
                        <h2 class="text-2xl font-bold mb-4 flex items-center gap-3">
                            <span class="flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 text-sm font-bold text-gray-500 group-hover:bg-[#FF6B35] group-hover:text-white transition-colors">3</span>
                            Account Registration
                        </h2>
                        <p class="text-gray-600 leading-relaxed pl-11">
                            You are responsible for maintaining the confidentiality of your account credentials. You agree to provide accurate, current, and complete information during the registration process. CARTAR reserves the right to suspend or terminate accounts that are suspected of fraudulent activity or unauthorized use.
                        </p>
                    </div>

                    <!-- Clause 4 -->
                    <div class="mb-10 scroll-mt-28 group" id="section-4">
                        <h2 class="text-2xl font-bold mb-4 flex items-center gap-3">
                            <span class="flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 text-sm font-bold text-gray-500 group-hover:bg-[#FF6B35] group-hover:text-white transition-colors">4</span>
                            Booking and Reservations
                        </h2>
                        <p class="text-gray-600 leading-relaxed pl-11">
                            Reservations are subject to vehicle availability. When you make a booking, a hold may be placed on your payment method. All transactions are processed in <strong class="text-gray-900">Nigerian Naira (₦)</strong>. We do not guarantee a specific make or model, but rather a vehicle class (e.g., Compact, SUV, Luxury).
                        </p>
                    </div>

                    <!-- Clause 5 -->
                    <div class="mb-10 scroll-mt-28 group" id="section-5">
                        <h2 class="text-2xl font-bold mb-4 flex items-center gap-3">
                            <span class="flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 text-sm font-bold text-gray-500 group-hover:bg-[#FF6B35] group-hover:text-white transition-colors">5</span>
                            Fees and Payments
                        </h2>
                        <div class="pl-11 space-y-3 text-gray-600">
                            <p class="leading-relaxed">
                                Rental fees are calculated based on daily rates. Additional charges may apply for:
                            </p>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
                                <div class="p-3 bg-gray-50 rounded-lg border border-gray-100">
                                    <h4 class="font-bold text-sm text-gray-900 mb-1">Fuel</h4>
                                    <p class="text-sm">Vehicles must be returned with the same fuel level as at pick-up.</p>
                                </div>
                                <div class="p-3 bg-gray-50 rounded-lg border border-gray-100">
                                    <h4 class="font-bold text-sm text-gray-900 mb-1">Mileage</h4>
                                    <p class="text-sm">Excess mileage fees apply if you exceed your daily limit.</p>
                                </div>
                                <div class="p-3 bg-gray-50 rounded-lg border border-gray-100">
                                    <h4 class="font-bold text-sm text-gray-900 mb-1">Late Returns</h4>
                                    <p class="text-sm">Returns more than 59 minutes late incur a full day charge.</p>
                                </div>
                                <div class="p-3 bg-gray-50 rounded-lg border border-gray-100">
                                    <h4 class="font-bold text-sm text-gray-900 mb-1">Tolls & Fines</h4>
                                    <p class="text-sm">Renter is liable for all traffic violations and toll fees.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Clause 6 -->
                    <div class="mb-10 scroll-mt-28 group" id="section-6">
                        <h2 class="text-2xl font-bold mb-4 flex items-center gap-3">
                            <span class="flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 text-sm font-bold text-gray-500 group-hover:bg-[#FF6B35] group-hover:text-white transition-colors">6</span>
                            Vehicle Use and Restrictions
                        </h2>
                        <p class="text-gray-600 leading-relaxed pl-11">
                            Vehicles must not be used for illegal purposes, off-road driving, racing, or transporting hazardous materials. Smoking is strictly prohibited in all CARTAR vehicles. A cleaning fee of up to ₦50,000 will be charged for violations of the no-smoking policy.
                        </p>
                    </div>

                    <!-- Clauses 7-12 -->
                    <div class="space-y-10">
                        <div class="mb-10 scroll-mt-28 group" id="section-7">
                            <h2 class="text-2xl font-bold mb-4 flex items-center gap-3">
                                <span class="flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 text-sm font-bold text-gray-500 group-hover:bg-[#E3655B] group-hover:text-white transition-colors">7</span>
                                Cancellations and Refunds
                            </h2>
                            <p class="text-gray-600 leading-relaxed pl-11">
                                Cancellations made 24 hours prior to the scheduled pick-up time are eligible for a full refund. Cancellations within 24 hours may incur a cancellation fee equivalent to one day's rental. No-shows will be charged the full rental amount.
                            </p>
                        </div>

                        <div class="mb-10 scroll-mt-28 group" id="section-8">
                            <h2 class="text-2xl font-bold mb-4 flex items-center gap-3">
                                <span class="flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 text-sm font-bold text-gray-500 group-hover:bg-[#FF6B35] group-hover:text-white transition-colors">8</span>
                                Liability and Insurance
                            </h2>
                            <p class="text-gray-600 leading-relaxed pl-11">
                                Basic insurance is included with all rentals. However, in the event of an accident where the Renter is at fault, the Renter is responsible for the insurance deductible. Optional waivers (CDW) are available for purchase to reduce liability.
                            </p>
                        </div>

                        <div class="mb-10 scroll-mt-28 group" id="section-9">
                            <h2 class="text-2xl font-bold mb-4 flex items-center gap-3">
                                <span class="flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 text-sm font-bold text-gray-500 group-hover:bg-[#FF6B35] group-hover:text-white transition-colors">9</span>
                                Force Majeure
                            </h2>
                            <p class="text-gray-600 leading-relaxed pl-11">
                                CARTAR shall not be liable for failure to deliver vehicles due to circumstances beyond our reasonable control, including but not limited to natural disasters, strikes, or government restrictions.
                            </p>
                        </div>

                        <div class="mb-10 scroll-mt-28 group" id="section-10">
                            <h2 class="text-2xl font-bold mb-4 flex items-center gap-3">
                                <span class="flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 text-sm font-bold text-gray-500 group-hover:bg-[#FF6B35] group-hover:text-white transition-colors">10</span>
                                Geographic Restrictions
                            </h2>
                            <p class="text-gray-600 leading-relaxed pl-11">
                                Unless explicitly authorized in writing, vehicles may not be driven outside the borders of Nigeria. GPS trackers are installed in all vehicles to ensure compliance.
                            </p>
                        </div>

                        <div class="mb-10 scroll-mt-28 group" id="section-11">
                            <h2 class="text-2xl font-bold mb-4 flex items-center gap-3">
                                <span class="flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 text-sm font-bold text-gray-500 group-hover:bg-[#FF6B35] group-hover:text-white transition-colors">11</span>
                                Termination of Agreement
                            </h2>
                            <p class="text-gray-600 leading-relaxed pl-11">
                                We reserve the right to terminate this agreement and repossess the vehicle without notice if the vehicle is found to be illegally parked, used in violation of the law, or if the Renter breaches any terms of this agreement.
                            </p>
                        </div>

                        <div class="mb-10 scroll-mt-28 group" id="section-12">
                            <h2 class="text-2xl font-bold mb-4 flex items-center gap-3">
                                <span class="flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 text-sm font-bold text-gray-500 group-hover:bg-[#FF6B35] group-hover:text-white transition-colors">12</span>
                                Governing Law
                            </h2>
                            <p class="text-gray-600 leading-relaxed pl-11">
                                These Terms are governed by and construed in accordance with the laws of the Federal Republic of Nigeria. Any disputes arising from these terms shall be subject to the exclusive jurisdiction of the courts in Lagos, Nigeria.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Divider -->
                <div class="w-full h-px bg-gray-200 my-12"></div>

                <!-- Contact Section -->
                <div class="rounded-xl overflow-hidden border border-gray-200 shadow-sm" id="section-contact">
                    <div class="bg-[#9CBF9B]/10 p-6 md:p-8">
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">Have Questions?</h3>
                        <p class="text-gray-600 mb-6">If you have any questions about our Terms of Service, please contact our support team. We are available 24/7 to assist you.</p>
                        <div class="grid md:grid-cols-3 gap-6">
                            <div class="flex flex-col gap-2">
                                <div class="flex items-center gap-2 text-[#FF6B35] font-bold">
                                    <span class="material-symbols-outlined">mail</span>
                                    Email Us
                                </div>
                                <a class="text-gray-900 hover:underline" href="mailto:support@cartar.ng">support@cartar.ng</a>
                            </div>
                            <div class="flex flex-col gap-2">
                                <div class="flex items-center gap-2 text-[#FF6B35] font-bold">
                                    <span class="material-symbols-outlined">call</span>
                                    Call Us
                                </div>
                                <a class="text-gray-900 hover:underline" href="tel:+2348001234567">+234 800 123 4567</a>
                            </div>
                            <div class="flex flex-col gap-2">
                                <div class="flex items-center gap-2 text-[#FF6B35] font-bold">
                                    <span class="material-symbols-outlined">location_on</span>
                                    Visit Us
                                </div>
                                <span class="text-gray-900">
                                    15 Admiralty Way, Lekki Phase 1<br/>Lagos, Nigeria
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
