<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;

class PaystackService
{
    protected string $secretKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->secretKey = config('paystack.secret_key');
        $this->baseUrl = config('paystack.payment_url');
    }

    /**
     * Initialize a payment transaction
     *
     * @param array $data Must contain: email, amount (in kobo), reference
     * @return array
     * @throws \Exception
     */
    public function initializeTransaction(array $data): array
    {
        // Amount should be in kobo (multiply Naira by 100)
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->secretKey,
            'Content-Type' => 'application/json',
        ])->post($this->baseUrl . '/transaction/initialize', [
            'email' => $data['email'],
            'amount' => $data['amount'] * 100, // Convert Naira to kobo
            'reference' => $data['reference'],
            'callback_url' => $data['callback_url'] ?? config('paystack.callback_url'),
            'metadata' => $data['metadata'] ?? [],
        ]);

        if (!$response->successful()) {
            throw new \Exception('Failed to initialize Paystack transaction: ' . $response->body());
        }

        $result = $response->json();

        if (!$result['status']) {
            throw new \Exception($result['message'] ?? 'Failed to initialize transaction');
        }

        return $result['data'];
    }

    /**
     * Verify a transaction by reference
     *
     * @param string $reference
     * @return array
     * @throws \Exception
     */
    public function verifyTransaction(string $reference): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->secretKey,
            'Content-Type' => 'application/json',
        ])->get($this->baseUrl . '/transaction/verify/' . $reference);

        if (!$response->successful()) {
            throw new \Exception('Failed to verify transaction: ' . $response->body());
        }

        $result = $response->json();

        if (!$result['status']) {
            throw new \Exception($result['message'] ?? 'Transaction verification failed');
        }

        return $result['data'];
    }

    /**
     * Check if transaction was successful
     *
     * @param array $transactionData
     * @return bool
     */
    public function isTransactionSuccessful(array $transactionData): bool
    {
        return isset($transactionData['status']) && $transactionData['status'] === 'success';
    }

    /**
     * Generate a unique payment reference
     *
     * @param int $bookingId
     * @return string
     */
    public static function generateReference(int $bookingId): string
    {
        return 'CARTAR-' . $bookingId . '-' . time() . '-' . strtoupper(substr(md5(uniqid()), 0, 6));
    }

    /**
     * Verify webhook signature from Paystack
     * 
     * Paystack signs webhooks using HMAC-SHA512 with your secret key.
     * This ensures the webhook is genuinely from Paystack.
     *
     * @param string $payload Raw request body (JSON)
     * @param string|null $signature Value from X-Paystack-Signature header
     * @return bool
     */
    public function verifyWebhookSignature(string $payload, ?string $signature): bool
    {
        if (empty($signature)) {
            return false;
        }

        $computedSignature = hash_hmac('sha512', $payload, $this->secretKey);
        
        // Use hash_equals to prevent timing attacks
        return hash_equals($computedSignature, $signature);
    }

    /**
     * Verify that the paid amount matches the expected amount
     *
     * @param int $paidAmountInKobo Amount from Paystack (in kobo)
     * @param float $expectedAmount Expected amount (in Naira)
     * @return bool
     */
    public function verifyAmount(int $paidAmountInKobo, float $expectedAmount): bool
    {
        $expectedAmountInKobo = (int) ($expectedAmount * 100);
        return $paidAmountInKobo === $expectedAmountInKobo;
    }
}
