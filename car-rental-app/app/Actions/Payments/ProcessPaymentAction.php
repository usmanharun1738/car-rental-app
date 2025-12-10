<?php

namespace App\Actions\Payments;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\PaymentAuditLog;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Services\PaystackService;

class ProcessPaymentAction
{
    public function __construct(
        protected PaystackService $paystackService
    ) {}

    /**
     * Initialize a Paystack payment for a booking
     *
     * @param Booking $booking
     * @param string|null $ipAddress
     * @param string|null $userAgent
     * @return array Contains 'payment', 'authorization_url', and 'reference'
     * @throws \Exception
     */
    public function execute(Booking $booking, ?string $ipAddress = null, ?string $userAgent = null): array
    {
        // Generate unique reference
        $reference = PaystackService::generateReference($booking->id);

        // Create pending payment record
        $payment = $booking->payments()->create([
            'amount' => $booking->total_price,
            'method' => PaymentMethod::PAYSTACK,
            'status' => PaymentStatus::PENDING,
            'transaction_reference' => $reference,
        ]);

        // Audit log: Payment initiated
        PaymentAuditLog::log(
            event: PaymentAuditLog::EVENT_INITIATED,
            paymentId: $payment->id,
            reference: $reference,
            ipAddress: $ipAddress,
            userAgent: $userAgent,
            metadata: [
                'booking_id' => $booking->id,
                'amount' => $booking->total_price,
                'user_id' => $booking->user_id,
            ]
        );

        // Initialize Paystack transaction
        $paystackData = $this->paystackService->initializeTransaction([
            'email' => $booking->user->email,
            'amount' => $booking->total_price,
            'reference' => $reference,
            'callback_url' => route('payment.callback'),
            'metadata' => [
                'booking_id' => $booking->id,
                'payment_id' => $payment->id,
                'user_id' => $booking->user_id,
            ],
        ]);

        return [
            'payment' => $payment,
            'authorization_url' => $paystackData['authorization_url'],
            'reference' => $reference,
        ];
    }
}
