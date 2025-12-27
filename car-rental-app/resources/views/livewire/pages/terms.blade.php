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

<div class="min-h-screen bg-gray-50 py-16 px-4 md:px-10">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Terms of Service</h1>
            <p class="text-gray-500">Last updated: {{ $lastUpdated }}</p>
        </div>
        
        <!-- Content -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 md:p-12 prose prose-gray max-w-none">
            
            <h2>1. Acceptance of Terms</h2>
            <p>By accessing and using CARTAR's car rental services, you agree to be bound by these Terms of Service. If you do not agree to these terms, please do not use our services.</p>
            
            <h2>2. Eligibility</h2>
            <p>To rent a vehicle from CARTAR, you must:</p>
            <ul>
                <li>Be at least 21 years of age</li>
                <li>Hold a valid driver's license for at least 2 years</li>
                <li>Provide a valid form of identification</li>
                <li>Have a valid payment method</li>
            </ul>
            
            <h2>3. Booking and Reservations</h2>
            <p>All bookings are subject to vehicle availability. We reserve the right to:</p>
            <ul>
                <li>Substitute a similar or upgraded vehicle if your reserved vehicle is unavailable</li>
                <li>Cancel bookings if payment cannot be processed</li>
                <li>Refuse service to anyone who violates these terms</li>
            </ul>
            
            <h2>4. Payment Terms</h2>
            <p>Payment is required at the time of booking. We accept:</p>
            <ul>
                <li>Credit and debit cards via Paystack</li>
                <li>Bank transfers (for corporate accounts)</li>
            </ul>
            <p>All prices are in Nigerian Naira (NGN) and include applicable taxes unless otherwise stated.</p>
            
            <h2>5. Vehicle Use</h2>
            <p>The rented vehicle must only be used for lawful purposes. You agree NOT to:</p>
            <ul>
                <li>Use the vehicle for racing, towing, or off-road driving</li>
                <li>Transport passengers for hire (taxi, ride-sharing)</li>
                <li>Smoke or consume alcohol/drugs in the vehicle</li>
                <li>Allow unauthorized drivers to operate the vehicle</li>
                <li>Transport hazardous materials</li>
                <li>Leave Nigeria without prior written authorization</li>
            </ul>
            
            <h2>6. Insurance and Liability</h2>
            <p>Basic insurance coverage is included in all rentals. However, you are responsible for:</p>
            <ul>
                <li>The deductible amount in case of an accident</li>
                <li>Damage caused by negligence or violation of these terms</li>
                <li>Personal belongings left in the vehicle</li>
                <li>Traffic violations and fines incurred during the rental period</li>
            </ul>
            
            <h2>7. Pick-up and Return</h2>
            <p>Vehicles must be returned at the agreed date, time, and location. Late returns will incur additional daily charges. The vehicle should be returned in the same condition as received, with the same fuel level.</p>
            
            <h2>8. Cancellation Policy</h2>
            <ul>
                <li><strong>Free cancellation:</strong> More than 48 hours before pick-up</li>
                <li><strong>50% refund:</strong> 24-48 hours before pick-up</li>
                <li><strong>No refund:</strong> Less than 24 hours before pick-up</li>
            </ul>
            
            <h2>9. Accidents and Incidents</h2>
            <p>In case of an accident, theft, or damage:</p>
            <ul>
                <li>Contact emergency services if needed</li>
                <li>Report to CARTAR immediately at +234 800 123 4567</li>
                <li>Do not admit liability to third parties</li>
                <li>Obtain a police report for all incidents</li>
            </ul>
            
            <h2>10. Limitation of Liability</h2>
            <p>CARTAR's liability is limited to the rental amount paid. We are not liable for indirect, consequential, or incidental damages arising from the use of our services.</p>
            
            <h2>11. Modifications</h2>
            <p>We reserve the right to modify these terms at any time. Continued use of our services after changes constitutes acceptance of the new terms.</p>
            
            <h2>12. Contact Us</h2>
            <p>For questions about these Terms of Service, please contact us:</p>
            <ul>
                <li>Email: legal@cartar.ng</li>
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
