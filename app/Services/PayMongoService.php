<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class PayMongoService
{
    /**
     * Create a hosted GCash checkout session.
     */
    public function createGcashCheckout(
        int $paymentId,
        string $referenceNumber,
        string $description,
        int $amountInCentavos,
        string $successUrl,
        string $cancelUrl
    ): array {
        $secretKey = (string) config('services.paymongo.secret_key');

        $baseUrl = rtrim(
            (string) config(
                'services.paymongo.base_url',
                'https://api.paymongo.com/v2'
            ),
            '/'
        );

        if ($secretKey === '') {
            throw new RuntimeException(
                'PayMongo is not configured yet. Add a test secret key to your .env file.'
            );
        }

        if ($amountInCentavos < 100) {
            throw new RuntimeException(
                'The GCash checkout amount must be at least PHP 1.00.'
            );
        }

        $response = Http::withBasicAuth($secretKey, '')
            ->acceptJson()
            ->asJson()
            ->timeout(30)
            ->post($baseUrl . '/checkout_sessions', [
                'data' => [
                    'attributes' => [
                        'line_items' => [
                            [
                                'currency' => 'PHP',
                                'amount' => $amountInCentavos,
                                'name' => $description,
                                'quantity' => 1,
                            ],
                        ],
                        'payment_method_types' => ['gcash'],
                        'success_url' => $successUrl,
                        'cancel_url' => $cancelUrl,
                        'reference_number' => $referenceNumber,
                        'description' => $description,
                        'metadata' => [
                            'local_payment_id' => (string) $paymentId,
                        ],
                    ],
                ],
            ]);

        if (! $response->successful()) {
            // Never log or expose the secret key.
            $message = $response->json('errors.0.detail')
                ?? 'PayMongo could not create the checkout session.';

            throw new RuntimeException(
                'PayMongo checkout creation failed: ' . $message
            );
        }

        $session = $response->json('data');

        $sessionId = data_get($session, 'id');
        $checkoutUrl = data_get($session, 'attributes.checkout_url');

        if (! is_string($sessionId) || ! is_string($checkoutUrl)) {
            throw new RuntimeException(
                'PayMongo returned an incomplete checkout response.'
            );
        }

        if (! str_starts_with($checkoutUrl, 'https://')) {
            throw new RuntimeException(
                'PayMongo returned an invalid checkout URL.'
            );
        }

        return [
            'id' => $sessionId,
            'checkout_url' => $checkoutUrl,
        ];
    }

    /**
     * Retrieve a checkout session directly from PayMongo.
     *
     * The caller must validate the returned session against the
     * local payment before marking any payment as paid.
     */
    public function retrieveCheckoutSession(string $sessionId): array
    {
        $secretKey = (string) config('services.paymongo.secret_key');

        $baseUrl = rtrim(
            (string) config(
                'services.paymongo.base_url',
                'https://api.paymongo.com/v2'
            ),
            '/'
        );

        if ($secretKey === '') {
            throw new RuntimeException(
                'PayMongo is not configured. Set the secret key first.'
            );
        }

        if (! preg_match('/^cs_[A-Za-z0-9_-]+$/', $sessionId)) {
            throw new RuntimeException(
                'Invalid checkout session ID.'
            );
        }

        $response = Http::withBasicAuth($secretKey, '')
            ->acceptJson()
            ->timeout(30)
            ->get(
                $baseUrl . '/checkout_sessions/' . rawurlencode($sessionId)
            );

        if (! $response->successful()) {
            throw new RuntimeException(
                'PayMongo could not verify the checkout session. HTTP status: '
                . $response->status()
            );
        }

        $session = $response->json('data');

        if (
            ! is_array($session)
            || data_get($session, 'id') !== $sessionId
        ) {
            throw new RuntimeException(
                'PayMongo returned an invalid checkout session.'
            );
        }

        return $session;
    }
}
