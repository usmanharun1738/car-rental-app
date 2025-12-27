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

<div class="min-h-screen bg-gray-50 py-16 px-4 md:px-10">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Privacy Policy</h1>
            <p class="text-gray-500">Last updated: {{ $lastUpdated }}</p>
        </div>
        
        <!-- Content -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 md:p-12 prose prose-gray max-w-none">
            
            <h2>1. Introduction</h2>
            <p>CARTAR ("we," "our," or "us") is committed to protecting your privacy. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you use our car rental services and website.</p>
            
            <h2>2. Information We Collect</h2>
            
            <h3>Personal Information</h3>
            <p>We collect information that you provide directly to us:</p>
            <ul>
                <li><strong>Account Information:</strong> Name, email address, phone number, password</li>
                <li><strong>Identity Documents:</strong> Driver's license, national ID card</li>
                <li><strong>Payment Information:</strong> Card details (processed securely by Paystack)</li>
                <li><strong>Booking Details:</strong> Pick-up/drop-off locations, dates, vehicle preferences</li>
            </ul>
            
            <h3>Automatically Collected Information</h3>
            <ul>
                <li>Device information (browser type, operating system)</li>
                <li>IP address and location data</li>
                <li>Usage data (pages viewed, time spent, click patterns)</li>
                <li>Cookies and similar tracking technologies</li>
            </ul>
            
            <h2>3. How We Use Your Information</h2>
            <p>We use the collected information to:</p>
            <ul>
                <li>Process and manage your vehicle rentals</li>
                <li>Verify your identity and driver's license</li>
                <li>Process payments securely</li>
                <li>Send booking confirmations and updates</li>
                <li>Provide customer support</li>
                <li>Improve our services and user experience</li>
                <li>Send promotional offers (with your consent)</li>
                <li>Comply with legal obligations</li>
            </ul>
            
            <h2>4. Information Sharing</h2>
            <p>We may share your information with:</p>
            <ul>
                <li><strong>Service Providers:</strong> Payment processors (Paystack), email services, analytics providers</li>
                <li><strong>Insurance Partners:</strong> To process insurance claims when applicable</li>
                <li><strong>Law Enforcement:</strong> When required by law or to protect our rights</li>
                <li><strong>Business Transfers:</strong> In connection with mergers or acquisitions</li>
            </ul>
            <p>We do NOT sell your personal information to third parties.</p>
            
            <h2>5. Data Security</h2>
            <p>We implement industry-standard security measures to protect your data:</p>
            <ul>
                <li>SSL/TLS encryption for all data transmissions</li>
                <li>Secure password hashing</li>
                <li>Regular security audits</li>
                <li>Limited employee access to personal data</li>
                <li>PCI-DSS compliant payment processing through Paystack</li>
            </ul>
            
            <h2>6. Data Retention</h2>
            <p>We retain your personal information for as long as necessary to:</p>
            <ul>
                <li>Provide our services to you</li>
                <li>Comply with legal obligations (tax records, accident reports)</li>
                <li>Resolve disputes and enforce agreements</li>
            </ul>
            <p>Typically, we retain booking records for 7 years after the rental date.</p>
            
            <h2>7. Your Rights</h2>
            <p>You have the right to:</p>
            <ul>
                <li><strong>Access:</strong> Request a copy of your personal data</li>
                <li><strong>Correction:</strong> Update inaccurate or incomplete information</li>
                <li><strong>Deletion:</strong> Request deletion of your data (subject to legal requirements)</li>
                <li><strong>Opt-out:</strong> Unsubscribe from marketing communications</li>
                <li><strong>Data Portability:</strong> Receive your data in a portable format</li>
            </ul>
            <p>To exercise these rights, contact us at privacy@cartar.ng</p>
            
            <h2>8. Cookies</h2>
            <p>We use cookies to:</p>
            <ul>
                <li>Remember your preferences and login status</li>
                <li>Analyze website traffic and usage patterns</li>
                <li>Provide personalized content</li>
            </ul>
            <p>You can control cookies through your browser settings, but some features may not function properly without them.</p>
            
            <h2>9. Children's Privacy</h2>
            <p>Our services are not intended for individuals under 18 years of age. We do not knowingly collect personal information from children.</p>
            
            <h2>10. International Transfers</h2>
            <p>Your data may be processed in countries outside Nigeria where our service providers are located. We ensure appropriate safeguards are in place for such transfers.</p>
            
            <h2>11. Changes to This Policy</h2>
            <p>We may update this Privacy Policy periodically. We will notify you of significant changes via email or prominent notice on our website.</p>
            
            <h2>12. Contact Us</h2>
            <p>For privacy-related inquiries:</p>
            <ul>
                <li>Email: privacy@cartar.ng</li>
                <li>Phone: +234 800 123 4567</li>
                <li>Address: Lagos, Nigeria</li>
            </ul>
            
        </div>
        
        <!-- Back Link -->
        <div class="text-center mt-8">
            <a href="{{ url('/') }}" class="text-[#FF6B35] hover:text-[#e55a2b] font-medium">
                ← Back to Home
            </a>
        </div>
    </div>
</div>
