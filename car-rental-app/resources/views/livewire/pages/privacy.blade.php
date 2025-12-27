<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

new #[Layout('components.layouts.guest')] #[Title('Privacy Policy - CARTAR')] class extends Component {
    public function with(): array
    {
        return [
            'lastUpdated' => 'December 27, 2025',
        ];
    }
}; ?>

<div class="min-h-screen bg-[#f6f7f8]">
    <!-- Hero Section -->
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
            <div class="max-w-3xl">
                <div class="inline-flex items-center rounded-full border border-[#9CBF9B]/30 bg-[#9CBF9B]/10 px-3 py-1 text-sm font-medium text-[#9CBF9B] mb-4">
                    Legal
                </div>
                <h1 class="text-4xl font-extrabold tracking-tight text-gray-900 sm:text-5xl mb-4">
                    Privacy Policy
                </h1>
                <p class="text-lg text-gray-600">
                    We are committed to transparency in how we handle your personal data.
                </p>
                <div class="mt-6 flex items-center gap-2 text-sm text-gray-500">
                    <span class="material-symbols-outlined text-lg">calendar_today</span>
                    <span>Last updated: <span class="font-medium text-gray-900">{{ $lastUpdated }}</span></span>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="lg:grid lg:grid-cols-12 lg:gap-12">
            <!-- Sidebar Table of Contents (Sticky) -->
            <div class="hidden lg:block lg:col-span-3">
                <nav aria-label="Sidebar" class="sticky top-24 space-y-1">
                    <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Contents</p>
                    <a class="text-gray-600 hover:bg-gray-50 group flex items-center px-3 py-2 text-sm font-medium rounded-md border-l-4 border-transparent hover:border-[#FF6B35] hover:text-[#FF6B35] transition-all" href="#section-1">
                        1. Introduction
                    </a>
                    <a class="text-gray-600 hover:bg-gray-50 group flex items-center px-3 py-2 text-sm font-medium rounded-md border-l-4 border-transparent hover:border-[#FF6B35] hover:text-[#FF6B35] transition-all" href="#section-2">
                        2. Information We Collect
                    </a>
                    <a class="text-gray-600 hover:bg-gray-50 group flex items-center px-3 py-2 text-sm font-medium rounded-md border-l-4 border-transparent hover:border-[#FF6B35] hover:text-[#FF6B35] transition-all" href="#section-3">
                        3. How We Use It
                    </a>
                    <a class="text-gray-600 hover:bg-gray-50 group flex items-center px-3 py-2 text-sm font-medium rounded-md border-l-4 border-transparent hover:border-[#FF6B35] hover:text-[#FF6B35] transition-all" href="#section-4">
                        4. Data Sharing
                    </a>
                    <a class="text-gray-600 hover:bg-gray-50 group flex items-center px-3 py-2 text-sm font-medium rounded-md border-l-4 border-transparent hover:border-[#FF6B35] hover:text-[#FF6B35] transition-all" href="#section-5">
                        5. Data Security
                    </a>
                    <a class="text-gray-600 hover:bg-gray-50 group flex items-center px-3 py-2 text-sm font-medium rounded-md border-l-4 border-transparent hover:border-[#FF6B35] hover:text-[#FF6B35] transition-all" href="#section-6">
                        6. Data Retention
                    </a>
                    <a class="text-gray-600 hover:bg-gray-50 group flex items-center px-3 py-2 text-sm font-medium rounded-md border-l-4 border-transparent hover:border-[#FF6B35] hover:text-[#FF6B35] transition-all" href="#section-7">
                        7. Your Rights
                    </a>
                    <a class="text-gray-600 hover:bg-gray-50 group flex items-center px-3 py-2 text-sm font-medium rounded-md border-l-4 border-transparent hover:border-[#FF6B35] hover:text-[#FF6B35] transition-all" href="#section-8">
                        8. Cookies Policy
                    </a>
                    <a class="text-gray-600 hover:bg-gray-50 group flex items-center px-3 py-2 text-sm font-medium rounded-md border-l-4 border-transparent hover:border-[#FF6B35] hover:text-[#FF6B35] transition-all" href="#section-9">
                        9. Third-party Links
                    </a>
                    <a class="text-gray-600 hover:bg-gray-50 group flex items-center px-3 py-2 text-sm font-medium rounded-md border-l-4 border-transparent hover:border-[#FF6B35] hover:text-[#FF6B35] transition-all" href="#section-10">
                        10. Children's Privacy
                    </a>
                    <a class="text-gray-600 hover:bg-gray-50 group flex items-center px-3 py-2 text-sm font-medium rounded-md border-l-4 border-transparent hover:border-[#FF6B35] hover:text-[#FF6B35] transition-all" href="#section-11">
                        11. Policy Changes
                    </a>
                    <a class="text-gray-600 hover:bg-gray-50 group flex items-center px-3 py-2 text-sm font-medium rounded-md border-l-4 border-transparent hover:border-[#FF6B35] hover:text-[#FF6B35] transition-all" href="#section-12">
                        12. Contact Us
                    </a>
                    <a class="mt-4 flex items-center gap-2 rounded-lg bg-[#9CBF9B]/10 px-3 py-2 text-sm font-bold text-green-700 hover:bg-[#9CBF9B]/20 transition-colors" href="#section-12">
                        <span class="material-symbols-outlined text-lg">support_agent</span>
                        Need Help?
                    </a>
                </nav>
            </div>

            <!-- Main Content Area -->
            <div class="lg:col-span-9">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <!-- Content Body -->
                    <div class="p-8 sm:p-10 space-y-12">
                        <!-- Section 1 -->
                        <section class="scroll-mt-28" id="section-1">
                            <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                                <span class="flex items-center justify-center w-8 h-8 rounded-full bg-[#9CBF9B]/20 text-[#9CBF9B] text-sm font-bold mr-3">1</span>
                                Introduction
                            </h2>
                            <p class="text-gray-600 leading-relaxed text-lg">
                                Welcome to CARTAR. We are committed to protecting your personal information and your right to privacy. This policy explains how we handle your data when you visit our website or use our car rental services in Nigeria. By accessing our services, you consent to the practices described in this policy.
                            </p>
                        </section>

                        <hr class="border-gray-100"/>

                        <!-- Section 2 -->
                        <section class="scroll-mt-28" id="section-2">
                            <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                                <span class="flex items-center justify-center w-8 h-8 rounded-full bg-[#CFD186]/20 text-yellow-700 text-sm font-bold mr-3">2</span>
                                Information We Collect
                            </h2>
                            <p class="text-gray-600 leading-relaxed mb-4">
                                We collect personal information that you provide to us voluntarily when you register on the website, express an interest in obtaining information about us or our products and services, or when you contact us.
                            </p>
                            <ul class="space-y-3 mt-4">
                                <li class="flex items-start">
                                    <span class="material-symbols-outlined text-[#9CBF9B] mr-3 mt-0.5">check_circle</span>
                                    <span class="text-gray-600"><strong>Identity Data:</strong> Name, address, government ID (NIN, Drivers License), and contact information.</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="material-symbols-outlined text-[#9CBF9B] mr-3 mt-0.5">check_circle</span>
                                    <span class="text-gray-600"><strong>Financial Data:</strong> Payment information processed via secure gateways in Naira (₦).</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="material-symbols-outlined text-[#9CBF9B] mr-3 mt-0.5">check_circle</span>
                                    <span class="text-gray-600"><strong>Vehicle Usage Data:</strong> GPS location, mileage, and driving behavior during your rental period.</span>
                                </li>
                            </ul>
                        </section>

                        <hr class="border-gray-100"/>

                        <!-- Section 3 -->
                        <section class="scroll-mt-28" id="section-3">
                            <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                                <span class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 text-blue-600 text-sm font-bold mr-3">3</span>
                                How We Use Your Information
                            </h2>
                            <p class="text-gray-600 leading-relaxed">
                                We use personal information collected via our website for a variety of business purposes described below. We process your personal information for these purposes in reliance on our legitimate business interests, in order to enter into or perform a contract with you, with your consent, and/or for compliance with our legal obligations.
                            </p>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                                <div class="p-4 bg-gray-50 rounded-lg border border-gray-100">
                                    <h4 class="font-bold text-gray-900 mb-2">Service Provision</h4>
                                    <p class="text-sm text-gray-500">To facilitate account creation, logon process, and car rental bookings.</p>
                                </div>
                                <div class="p-4 bg-gray-50 rounded-lg border border-gray-100">
                                    <h4 class="font-bold text-gray-900 mb-2">Safety & Security</h4>
                                    <p class="text-sm text-gray-500">To monitor vehicle safety and prevent fraud or theft.</p>
                                </div>
                            </div>
                        </section>

                        <hr class="border-gray-100"/>

                        <!-- Section 4 -->
                        <section class="scroll-mt-28" id="section-4">
                            <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                                <span class="flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 text-gray-500 text-sm font-bold mr-3">4</span>
                                Data Sharing
                            </h2>
                            <p class="text-gray-600 leading-relaxed">
                                We may process or share your data that we hold based on the following legal basis: Consent, Legitimate Interests, Performance of a Contract, or Legal Obligations. We may share data with third-party vendors, service providers, contractors, or agents who perform services for us or on our behalf and require access to such information to do that work.
                            </p>
                        </section>

                        <hr class="border-gray-100"/>

                        <!-- Section 5 -->
                        <section class="scroll-mt-28" id="section-5">
                            <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                                <span class="flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 text-gray-500 text-sm font-bold mr-3">5</span>
                                Data Security
                            </h2>
                            <p class="text-gray-600 leading-relaxed">
                                We have implemented appropriate technical and organizational security measures designed to protect the security of any personal information we process. However, please also remember that we cannot guarantee that the internet itself is 100% secure.
                            </p>
                        </section>

                        <hr class="border-gray-100"/>

                        <!-- Section 6 -->
                        <section class="scroll-mt-28" id="section-6">
                            <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                                <span class="flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 text-gray-500 text-sm font-bold mr-3">6</span>
                                Data Retention
                            </h2>
                            <p class="text-gray-600 leading-relaxed">
                                We will only keep your personal information for as long as it is necessary for the purposes set out in this privacy policy, unless a longer retention period is required or permitted by law. We typically retain booking records for 7 years after the rental date.
                            </p>
                        </section>

                        <hr class="border-gray-100"/>

                        <!-- Section 7 -->
                        <section class="scroll-mt-28" id="section-7">
                            <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                                <span class="flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 text-gray-500 text-sm font-bold mr-3">7</span>
                                Your Rights
                            </h2>
                            <p class="text-gray-600 leading-relaxed">
                                In some regions (like Nigeria), you have certain rights under applicable data protection laws. These may include the right (i) to request access and obtain a copy of your personal information, (ii) to request rectification or erasure, (iii) to restrict the processing of your personal information, and (iv) if applicable, to data portability.
                            </p>
                        </section>

                        <hr class="border-gray-100"/>

                        <!-- Section 8 -->
                        <section class="scroll-mt-28" id="section-8">
                            <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                                <span class="flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 text-gray-500 text-sm font-bold mr-3">8</span>
                                Cookies Policy
                            </h2>
                            <p class="text-gray-600 leading-relaxed">
                                We may use cookies and similar tracking technologies to access or store information. Specific information about how we use such technologies and how you can refuse certain cookies is set out in our Cookie Notice. You can control cookies through your browser settings.
                            </p>
                        </section>

                        <hr class="border-gray-100"/>

                        <!-- Section 9 -->
                        <section class="scroll-mt-28" id="section-9">
                            <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                                <span class="flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 text-gray-500 text-sm font-bold mr-3">9</span>
                                Third-party Links
                            </h2>
                            <p class="text-gray-600 leading-relaxed">
                                Our website may contain advertisements from third parties that are not affiliated with us and which may link to other websites, online services, or mobile applications. We cannot guarantee the safety and privacy of data you provide to any third parties.
                            </p>
                        </section>

                        <hr class="border-gray-100"/>

                        <!-- Section 10 -->
                        <section class="scroll-mt-28" id="section-10">
                            <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                                <span class="flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 text-gray-500 text-sm font-bold mr-3">10</span>
                                Children's Privacy
                            </h2>
                            <p class="text-gray-600 leading-relaxed">
                                We do not knowingly solicit data from or market to children under 18 years of age. By using the Services, you represent that you are at least 18 or that you are the parent or guardian of such a minor and consent to such minor dependent's use of the Services.
                            </p>
                        </section>

                        <hr class="border-gray-100"/>

                        <!-- Section 11 -->
                        <section class="scroll-mt-28" id="section-11">
                            <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                                <span class="flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 text-gray-500 text-sm font-bold mr-3">11</span>
                                Changes to This Policy
                            </h2>
                            <p class="text-gray-600 leading-relaxed">
                                We may update this privacy notice from time to time. The updated version will be indicated by an updated "Revised" date and the updated version will be effective as soon as it is accessible. We encourage you to review this privacy notice frequently to be informed of how we are protecting your information.
                            </p>
                        </section>

                        <hr class="border-gray-100"/>

                        <!-- Section 12: Contact Us -->
                        <section class="scroll-mt-28" id="section-12">
                            <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                                <span class="flex items-center justify-center w-8 h-8 rounded-full bg-[#E3655B]/20 text-[#E3655B] text-sm font-bold mr-3">12</span>
                                Contact Us
                            </h2>
                            <div class="bg-gray-50 rounded-2xl p-6 sm:p-8 border border-gray-100">
                                <p class="text-gray-600 mb-8">
                                    If you have questions or comments about this policy, you may email us or contact us by post at:
                                </p>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                                    <!-- Email -->
                                    <div class="flex flex-col items-start p-4 bg-white rounded-xl shadow-sm border border-gray-100 w-full hover:border-[#9CBF9B] transition-colors group">
                                        <div class="w-10 h-10 rounded-lg bg-[#9CBF9B]/10 text-[#9CBF9B] flex items-center justify-center mb-3 group-hover:bg-[#9CBF9B] group-hover:text-white transition-colors">
                                            <span class="material-symbols-outlined">mail</span>
                                        </div>
                                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Email Us</span>
                                        <a class="text-sm font-bold text-gray-900 hover:text-[#FF6B35] mt-1" href="mailto:privacy@cartar.ng">privacy@cartar.ng</a>
                                    </div>
                                    <!-- Phone -->
                                    <div class="flex flex-col items-start p-4 bg-white rounded-xl shadow-sm border border-gray-100 w-full hover:border-[#CFD186] transition-colors group">
                                        <div class="w-10 h-10 rounded-lg bg-[#CFD186]/20 text-yellow-700 flex items-center justify-center mb-3 group-hover:bg-[#CFD186] group-hover:text-white transition-colors">
                                            <span class="material-symbols-outlined">call</span>
                                        </div>
                                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Call Us</span>
                                        <a class="text-sm font-bold text-gray-900 hover:text-[#FF6B35] mt-1" href="tel:+2348001234567">+234 800 123 4567</a>
                                    </div>
                                    <!-- Location -->
                                    <div class="flex flex-col items-start p-4 bg-white rounded-xl shadow-sm border border-gray-100 w-full hover:border-[#E3655B] transition-colors group">
                                        <div class="w-10 h-10 rounded-lg bg-[#E3655B]/10 text-[#E3655B] flex items-center justify-center mb-3 group-hover:bg-[#E3655B] group-hover:text-white transition-colors">
                                            <span class="material-symbols-outlined">location_on</span>
                                        </div>
                                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Visit Us</span>
                                        <span class="text-sm font-bold text-gray-900 mt-1">14 Victoria Island,<br/>Lagos, Nigeria</span>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>

                <!-- Back Link -->
                <div class="text-center mt-8">
                    <a href="{{ route('home') }}" wire:navigate class="inline-flex items-center gap-2 text-[#FF6B35] hover:text-[#e55a2b] font-medium transition-colors">
                        <span class="material-symbols-outlined text-sm">arrow_back</span>
                        Back to Home
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
