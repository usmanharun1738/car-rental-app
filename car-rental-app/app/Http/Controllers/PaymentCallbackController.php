<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Enums\PaymentStatus;
use App\Enums\BookingStatus;
use App\Services\PaystackService;
use Illuminate\Http\Request;

class PaymentCallbackController extends Controller
{
    public function __construct(
        protected PaystackService $paystackService
    ) {}

    /**
     * Handle Paystack payment callback
     */
    public function handle(Request $request)
    {
        $reference = $request->query('reference');

        if (!$reference) {
            return redirect()->route('dashboard')
                ->with('error', 'Invalid payment reference.');
        }

        // Find the payment by reference
        $payment = Payment::where('transaction_reference', $reference)->first();

        if (!$payment) {
            return redirect()->route('dashboard')
                ->with('error', 'Payment record not found.');
        }

        try {
            // Idempotency check: If already paid, just redirect with success
            if ($payment->status === PaymentStatus::PAID) {
                return redirect()->route('dashboard')
                    ->with('info', 'Payment already processed. Your booking is confirmed.');
            }

            // Verify transaction with Paystack
            $transactionData = $this->paystackService->verifyTransaction($reference);

            if ($this->paystackService->isTransactionSuccessful($transactionData)) {
                // Amount verification: Ensure paid amount matches expected amount
                $paidAmount = $transactionData['amount'] ?? 0; // Amount in kobo
                
                if (!$this->paystackService->verifyAmount($paidAmount, (float) $payment->amount)) {
                    \Log::error('Payment callback: Amount mismatch', [
                        'reference' => $reference,
                        'expected_kobo' => $payment->amount * 100,
                        'received_kobo' => $paidAmount,
                    ]);
                    
                    $payment->update(['status' => PaymentStatus::FAILED]);
                    
                    return redirect()->route('dashboard')
                        ->with('error', 'Payment amount mismatch. Please contact support.');
                }

                // Update payment status
                $payment->update([
                    'status' => PaymentStatus::PAID,
                ]);

                // Update booking status to confirmed
                $booking = $payment->payable;
                if ($booking) {
                    $booking->update([
                        'status' => BookingStatus::CONFIRMED,
                    ]);
                }

                return redirect()->route('booking.success', $booking)
                    ->with('success', 'Payment successful! Your booking has been confirmed.');
            } else {
                // Payment failed
                $payment->update([
                    'status' => PaymentStatus::FAILED,
                ]);

                return redirect()->route('dashboard')
                    ->with('error', 'Payment failed. Please try again.');
            }
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Payment verification failed: ' . $e->getMessage(), [
                'reference' => $reference,
                'payment_id' => $payment->id,
            ]);

            return redirect()->route('dashboard')
                ->with('error', 'Payment verification failed. Please contact support if your payment was deducted.');
        }
    }
}
