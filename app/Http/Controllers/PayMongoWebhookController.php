<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\PayMongoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use UnexpectedValueException;

class PayMongoWebhookController extends Controller
{
    public function handle(Request $request, PayMongoService $payMongo)
    {
        $rawBody = $request->getContent();

        $signatureHeader = (string) $request->header(
            'Paymongo-Signature',
            ''
        );

        $webhookSecret = (string) config(
            'services.paymongo.webhook_secret'
        );

        if (
            $webhookSecret === ''
            || $signatureHeader === ''
            || $rawBody === ''
        ) {
            return response()->json([
                'message' => 'Webhook configuration or signature missing.',
            ], 400);
        }

        // Parse PayMongo's signature header.
        $parts = [];

        foreach (explode(',', $signatureHeader) as $part) {
            $pair = explode('=', trim($part), 2);

            if (count($pair) === 2) {
                $parts[$pair[0]] = $pair[1];
            }
        }

        $timestamp = $parts['t'] ?? '';
        $event = json_decode($rawBody, true);

        if (
            ! is_string($timestamp)
            || ! ctype_digit($timestamp)
            || ! is_array($event)
            || abs(time() - (int) $timestamp) > 300
        ) {
            return response()->json([
                'message' => 'Invalid or expired webhook.',
            ], 400);
        }

        $eventAttributes = data_get($event, 'data.attributes', []);

        $isLive = data_get($eventAttributes, 'livemode', false) === true;

        $providedSignature = $isLive
            ? ($parts['li'] ?? '')
            : ($parts['te'] ?? '');

        $expectedSignature = hash_hmac(
            'sha256',
            $timestamp . '.' . $rawBody,
            $webhookSecret
        );

        if (
            ! is_string($providedSignature)
            || $providedSignature === ''
            || ! hash_equals($expectedSignature, $providedSignature)
        ) {
            return response()->json([
                'message' => 'Invalid webhook signature.',
            ], 401);
        }

        // Ignore events that do not confirm a paid checkout session.
        if (
            data_get($eventAttributes, 'type')
            !== 'checkout_session.payment.paid'
        ) {
            return response()->json([
                'received' => true,
                'ignored' => true,
            ]);
        }

        $eventId = data_get($event, 'data.id');
        $sessionId = data_get(
            $eventAttributes,
            'data.id'
        );

        if (
            ! is_string($eventId)
            || $eventId === ''
            || ! is_string($sessionId)
            || ! preg_match('/^cs_[A-Za-z0-9_-]+$/', $sessionId)
        ) {
            return response()->json([
                'message' => 'Missing or invalid event/session ID.',
            ], 400);
        }

        // The event's embedded payment details are not sufficient on their own.
        // Retrieve the session directly from PayMongo using our secret key.
        try {
            $verifiedSession = $payMongo->retrieveCheckoutSession(
                $sessionId
            );
        } catch (RuntimeException $exception) {
            Log::warning('PayMongo checkout verification could not complete.', [
                'session_id' => $sessionId,
                'event_id' => $eventId,
                'exception' => get_class($exception),
            ]);

            // Return a server error so PayMongo can retry the webhook.
            return response()->json([
                'message' => 'Unable to verify checkout session with PayMongo.',
            ], 500);
        } catch (\Throwable $exception) {
            Log::error('Unexpected PayMongo verification error.', [
                'session_id' => $sessionId,
                'event_id' => $eventId,
                'exception' => get_class($exception),
            ]);

            return response()->json([
                'message' => 'Checkout verification failed.',
            ], 500);
        }

        $sessionAttributes = data_get(
            $verifiedSession,
            'attributes',
            []
        );

        if (data_get($verifiedSession, 'id') !== $sessionId) {
            return response()->json([
                'message' => 'Retrieved checkout session ID does not match.',
            ], 400);
        }

        // Ensure the webhook and configured API key use the same mode.
        $secretKey = (string) config('services.paymongo.secret_key');

        if ($secretKey === '') {
            return response()->json([
                'message' => 'PayMongo secret key is not configured.',
            ], 500);
        }

        $keyIsLive = str_starts_with($secretKey, 'sk_live_');

        if (
            $isLive !== $keyIsLive
            || data_get($sessionAttributes, 'livemode') !== $isLive
        ) {
            Log::warning('PayMongo mode mismatch.', [
                'session_id' => $sessionId,
                'event_id' => $eventId,
            ]);

            return response()->json([
                'message' => 'Webhook and checkout mode do not match.',
            ], 400);
        }

        // Inspect the payment details returned by PayMongo's API,
        // rather than trusting the embedded webhook payload.
        $sessionPayments = data_get(
            $sessionAttributes,
            'payments',
            []
        );

        if (! is_array($sessionPayments)) {
            return response()->json([
                'message' => 'PayMongo returned an invalid payments list.',
            ], 400);
        }

        $paidPayment = collect($sessionPayments)->first(
            fn ($item) => is_array($item)
                && data_get($item, 'attributes.status') === 'paid'
        );

        if (! is_array($paidPayment)) {
            return response()->json([
                'message' => 'PayMongo has not confirmed a paid transaction.',
            ], 400);
        }

        $paidAttributes = data_get(
            $paidPayment,
            'attributes',
            []
        );

        $currency = strtoupper(
            (string) data_get($paidAttributes, 'currency', '')
        );

        $amountInCentavos = data_get(
            $paidAttributes,
            'amount'
        );

        $paymentIntentId = data_get(
            $paidPayment,
            'attributes.payment_intent_id'
        ) ?? data_get(
            $sessionAttributes,
            'payment_intent.id'
        );

        if (
            $currency !== 'PHP'
            || ! is_numeric($amountInCentavos)
            || (int) $amountInCentavos < 100
            || (float) $amountInCentavos !== (float) (int) $amountInCentavos
        ) {
            return response()->json([
                'message' => 'PayMongo returned an invalid amount or currency.',
            ], 400);
        }

        try {
            DB::transaction(function () use (
                $sessionId,
                $eventId,
                $paymentIntentId,
                $amountInCentavos,
                $isLive
            ) {
                $payment = Payment::where(
                    'paymongo_checkout_session_id',
                    $sessionId
                )
                    ->lockForUpdate()
                    ->first();

                if (
                    ! $payment
                    || $payment->payment_method !== 'gcash'
                ) {
                    throw new UnexpectedValueException(
                        'Checkout session does not match a local GCash payment.'
                    );
                }

                // A repeated delivery for an already-paid transaction is harmless.
                if ($payment->status === 'paid') {
                    return;
                }

                if ($payment->status !== 'pending') {
                    throw new UnexpectedValueException(
                        'Local payment is not pending.'
                    );
                }

                $expectedAmount = (int) round(
                    ((float) $payment->amount) * 100
                );

                if ((int) $amountInCentavos !== $expectedAmount) {
                    throw new UnexpectedValueException(
                        'Verified PayMongo amount does not match the local payment.'
                    );
                }

                // Do not overwrite an event ID already associated with another payment.
                $eventAlreadyUsed = Payment::where(
                    'paymongo_webhook_event_id',
                    $eventId
                )
                    ->where('id', '!=', $payment->id)
                    ->exists();

                if ($eventAlreadyUsed) {
                    throw new UnexpectedValueException(
                        'Webhook event has already been associated with another payment.'
                    );
                }

                if (
                    is_string($paymentIntentId)
                    && $paymentIntentId !== ''
                ) {
                    $intentAlreadyUsed = Payment::where(
                        'paymongo_payment_intent_id',
                        $paymentIntentId
                    )
                        ->where('id', '!=', $payment->id)
                        ->exists();

                    if ($intentAlreadyUsed) {
                        throw new UnexpectedValueException(
                            'Payment intent has already been associated with another payment.'
                        );
                    }

                    $payment->paymongo_payment_intent_id = $paymentIntentId;
                }

                $payment->status = 'paid';
                $payment->paid_at = now();
                $payment->payment_date = now()->toDateString();
                $payment->paymongo_webhook_event_id = $eventId;
                $payment->save();
            }, 3);
        } catch (UnexpectedValueException $exception) {
            Log::warning('PayMongo webhook rejected.', [
                'reason' => $exception->getMessage(),
                'session_id' => $sessionId,
                'event_id' => $eventId,
            ]);

            return response()->json([
                'message' => 'Webhook did not match a valid pending payment.',
            ], 400);
        } catch (\Throwable $exception) {
            Log::error('PayMongo webhook processing failed.', [
                'session_id' => $sessionId,
                'event_id' => $eventId,
                'exception' => get_class($exception),
            ]);

            return response()->json([
                'message' => 'Webhook processing failed.',
            ], 500);
        }

        return response()->json([
            'received' => true,
        ]);
    }
}
