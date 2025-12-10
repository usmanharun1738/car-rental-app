<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\PaymentAuditLog;
use App\Enums\PaymentStatus;
use App\Enums\BookingStatus;
use App\Services\PaystackService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaystackWebhookController extends Controller
{
    public function __construct(
        protected PaystackService $paystackService
    ) {}

    /**
     * Handle Paystack webhook events
     * 
     * Paystack sends webhooks for various events. We primarily care about:
     * - charge.success: Payment was successful
     */
    public function handle(Request $request)
    {
        // Get raw payload and signature
        $payload = $request->getContent();
        $signature = $request->header('X-Paystack-Signature');

        // Verify signature - reject if invalid
        if (!$this->paystackService->verifyWebhookSignature($payload, $signature)) {
            Log::warning('Paystack webhook: Invalid signature attempted', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Audit log: Invalid signature attempt
            PaymentAuditLog::log(
                event: PaymentAuditLog::EVENT_INVALID_SIGNATURE,
                ipAddress: $request->ip(),
                userAgent: $request->userAgent(),
                metadata: ['signature_present' => !empty($signature)]
            );
            
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        // Parse the event
        $event = json_decode($payload, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('Paystack webhook: Invalid JSON payload');
            return response()->json(['error' => 'Invalid payload'], 400);
        }

        $reference = $event['data']['reference'] ?? null;

        // Audit log: Webhook received
        PaymentAuditLog::log(
            event: PaymentAuditLog::EVENT_WEBHOOK_RECEIVED,
            reference: $reference,
            ipAddress: $request->ip(),
            userAgent: $request->userAgent(),
            metadata: ['event_type' => $event['event'] ?? 'unknown']
        );

        Log::info('Paystack webhook received', [
            'event' => $event['event'] ?? 'unknown',
            'reference' => $reference,
        ]);

        // Handle the event
        $eventType = $event['event'] ?? null;

        switch ($eventType) {
            case 'charge.success':
                return $this->handleChargeSuccess($event['data'], $request);
            
            default:
                // Acknowledge receipt of unhandled events
                Log::info('Paystack webhook: Unhandled event type', ['event' => $eventType]);
                return response()->json(['message' => 'Event received'], 200);
        }
    }

    /**
     * Handle successful charge event
     */
    private function handleChargeSuccess(array $data, Request $request): \Illuminate\Http\JsonResponse
    {
        $reference = $data['reference'] ?? null;
        $paidAmount = $data['amount'] ?? 0; // Amount in kobo

        if (!$reference) {
            Log::error('Paystack webhook: Missing reference in charge.success');
            return response()->json(['error' => 'Missing reference'], 400);
        }

        // Find the payment by reference
        $payment = Payment::where('transaction_reference', $reference)->first();

        if (!$payment) {
            Log::warning('Paystack webhook: Payment not found', ['reference' => $reference]);
            return response()->json(['error' => 'Payment not found'], 404);
        }

        // Idempotency check: Skip if already processed
        if ($payment->status === PaymentStatus::PAID) {
            Log::info('Paystack webhook: Payment already processed', [
                'reference' => $reference,
                'payment_id' => $payment->id,
            ]);
            return response()->json(['message' => 'Already processed'], 200);
        }

        // Verify amount matches
        if (!$this->paystackService->verifyAmount($paidAmount, (float) $payment->amount)) {
            Log::error('Paystack webhook: Amount mismatch', [
                'reference' => $reference,
                'expected_kobo' => $payment->amount * 100,
                'received_kobo' => $paidAmount,
            ]);

            // Audit log: Amount mismatch
            PaymentAuditLog::log(
                event: PaymentAuditLog::EVENT_AMOUNT_MISMATCH,
                paymentId: $payment->id,
                reference: $reference,
                ipAddress: $request->ip(),
                metadata: [
                    'expected_kobo' => $payment->amount * 100,
                    'received_kobo' => $paidAmount,
                ]
            );
            
            // Still mark as failed to prevent retries with wrong amount
            $payment->update(['status' => PaymentStatus::FAILED]);
            
            return response()->json(['error' => 'Amount mismatch'], 400);
        }

        // Update payment status
        $payment->update([
            'status' => PaymentStatus::PAID,
        ]);

        // Update booking status
        $booking = $payment->payable;
        if ($booking) {
            $booking->update([
                'status' => BookingStatus::CONFIRMED,
            ]);

            // Audit log: Payment verified
            PaymentAuditLog::log(
                event: PaymentAuditLog::EVENT_VERIFIED,
                paymentId: $payment->id,
                reference: $reference,
                ipAddress: $request->ip(),
                metadata: [
                    'booking_id' => $booking->id,
                    'amount_kobo' => $paidAmount,
                ]
            );

            Log::info('Paystack webhook: Payment confirmed', [
                'reference' => $reference,
                'payment_id' => $payment->id,
                'booking_id' => $booking->id,
            ]);
        }

        return response()->json(['message' => 'Payment confirmed'], 200);
    }
}

