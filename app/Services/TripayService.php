<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TripayService
{
    private string $apiKey;
    private string $privateKey;
    private string $merchantCode;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('tripay.api_key');
        $this->privateKey = config('tripay.private_key');
        $this->merchantCode = config('tripay.merchant_code');
        $this->baseUrl = config('tripay.base_url');
    }

    /**
     * Get active payment channels from Tripay.
     */
    public function getPaymentChannels(): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
        ])->get($this->baseUrl . '/merchant/payment-channel');

        $data = $response->json();

        if (! ($data['success'] ?? false)) {
            Log::error('Tripay getPaymentChannels failed', ['response' => $data]);
            return [];
        }

        return $data['data'] ?? [];
    }

    /**
     * Calculate transaction fee for a specific channel and amount.
     */
    public function calculateFee(string $channelCode, int $amount): ?array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
        ])->get($this->baseUrl . '/merchant/fee-calculator', [
            'code' => $channelCode,
            'amount' => $amount,
        ]);

        $data = $response->json();

        if (! ($data['success'] ?? false)) {
            Log::error('Tripay calculateFee failed', ['response' => $data]);
            return null;
        }

        return $data['data'][0] ?? null;
    }

    /**
     * Create a Closed Payment transaction.
     *
     * @param  array{
     *   method: string,
     *   merchant_ref: string,
     *   amount: int,
     *   customer_name: string,
     *   customer_email: string|null,
     *   customer_phone: string|null,
     *   order_items: array,
     *   return_url: string|null,
     *   expired_time: int|null,
     * } $params
     */
    public function createTransaction(array $params): array
    {
        $merchantRef = $params['merchant_ref'];
        $amount = (int) $params['amount'];

        $signature = hash_hmac(
            'sha256',
            $this->merchantCode . $merchantRef . $amount,
            $this->privateKey
        );

        $payload = [
            'method' => $params['method'],
            'merchant_ref' => $merchantRef,
            'amount' => $amount,
            'customer_name' => $params['customer_name'] ?? 'Pelanggan',
            'customer_email' => $params['customer_email'] ?? null,
            'customer_phone' => $params['customer_phone'] ?? null,
            'order_items' => $params['order_items'] ?? [],
            'callback_url' => config('tripay.callback_url') ?: url('/api/payment/callback'),
            'return_url' => $params['return_url'] ?? config('tripay.return_url') ?: url('/portal-pelanggan'),
            'expired_time' => $params['expired_time'] ?? (time() + config('tripay.expiry_seconds', 86400)),
            'signature' => $signature,
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
        ])->post($this->baseUrl . '/transaction/create', $payload);

        $data = $response->json();

        if (! ($data['success'] ?? false)) {
            Log::error('Tripay createTransaction failed', [
                'payload' => array_diff_key($payload, ['signature' => '']),
                'response' => $data,
            ]);

            return [
                'success' => false,
                'message' => $data['message'] ?? 'Gagal membuat transaksi Tripay',
            ];
        }

        Log::info('Tripay transaction created', [
            'reference' => $data['data']['reference'] ?? null,
            'merchant_ref' => $merchantRef,
        ]);

        return [
            'success' => true,
            'data' => $data['data'],
        ];
    }

    /**
     * Get transaction detail by reference.
     */
    public function getTransactionDetail(string $reference): ?array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
        ])->get($this->baseUrl . '/transaction/detail', [
            'reference' => $reference,
        ]);

        $data = $response->json();

        if (! ($data['success'] ?? false)) {
            return null;
        }

        return $data['data'] ?? null;
    }

    /**
     * Validate callback signature from Tripay.
     *
     * Tripay callback sends JSON with a signature in the X-Callback-Signature header.
     * The signature is HMAC-SHA256 of the raw JSON body using private key.
     */
    public function validateCallbackSignature(string $rawBody, string $callbackSignature): bool
    {
        $expected = hash_hmac('sha256', $rawBody, $this->privateKey);

        return hash_equals($expected, $callbackSignature);
    }

    /**
     * Check if credentials are configured.
     */
    public function isConfigured(): bool
    {
        return ! empty($this->apiKey)
            && ! empty($this->privateKey)
            && ! empty($this->merchantCode);
    }
}
