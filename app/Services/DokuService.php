<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DokuService
{
    protected string $clientId;
    protected string $secretKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->clientId  = config('doku.client_id');
        $this->secretKey = config('doku.secret_key');
        $this->baseUrl   = config('doku.base_url');
    }

    /**
     * Create a DOKU Checkout session and return the payment URL.
     *
     * @param  array{invoice_number: string, amount: int, customer_name: string, customer_email: string}  $params
     * @return array{payment_url: string, request_id: string, invoice_number: string}
     *
     * @throws \RuntimeException
     */
    public function createCheckout(array $params): array
    {
        $requestId        = Str::uuid()->toString();
        $requestTimestamp  = gmdate('Y-m-d\TH:i:s\Z');
        $requestTarget    = '/checkout/v1/payment';

        $body = [
            'order' => [
                'amount'         => (int) $params['amount'],
                'invoice_number' => $params['invoice_number'],
                'callback_url'   => route('orders.confirmation', $params['invoice_number']),
                'auto_redirect'  => true,
            ],
            'payment' => [
                'payment_due_date' => (int) config('doku.payment_due_minutes', 60),
            ],
            'customer' => [
                'name'  => $params['customer_name'] ?? 'Customer',
                'email' => $params['customer_email'] ?? '',
            ],
        ];

        $jsonBody  = json_encode($body, JSON_UNESCAPED_SLASHES);
        $signature = $this->generateSignature($requestId, $requestTimestamp, $requestTarget, $jsonBody);

        Log::info('DOKU Checkout Request', [
            'request_id'  => $requestId,
            'invoice'     => $params['invoice_number'],
            'amount'      => $params['amount'],
        ]);

        $response = Http::withHeaders([
            'Client-Id'         => $this->clientId,
            'Request-Id'        => $requestId,
            'Request-Timestamp' => $requestTimestamp,
            'Signature'         => $signature,
            'Content-Type'      => 'application/json',
        ])->withBody($jsonBody, 'application/json')
          ->post($this->baseUrl . $requestTarget);

        if (! $response->successful()) {
            Log::error('DOKU Checkout Failed', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            throw new \RuntimeException(
                'Gagal membuat sesi pembayaran DOKU. Silakan coba lagi. (HTTP ' . $response->status() . ')'
            );
        }

        $data = $response->json();

        Log::info('DOKU Checkout Response', [
            'response' => $data,
        ]);

        $paymentUrl = $data['response']['payment']['url'] ?? null;

        if (! $paymentUrl) {
            throw new \RuntimeException(
                'DOKU tidak mengembalikan URL pembayaran. Silakan coba lagi.'
            );
        }

        return [
            'payment_url'    => $paymentUrl,
            'request_id'     => $requestId,
            'invoice_number' => $params['invoice_number'],
        ];
    }

    /**
     * Verify the signature from a DOKU notification/webhook.
     */
    public function verifyNotificationSignature(
        string $signatureHeader,
        string $requestId,
        string $requestTimestamp,
        string $requestTarget,
        string $rawBody
    ): bool {
        $digest = base64_encode(hash('sha256', $rawBody, true));

        $rawSignature = "Client-Id:{$this->clientId}\n"
            . "Request-Id:{$requestId}\n"
            . "Request-Timestamp:{$requestTimestamp}\n"
            . "Request-Target:{$requestTarget}\n"
            . "Digest:{$digest}";

        $expectedSignature = 'HMACSHA256=' . base64_encode(
            hash_hmac('sha256', $rawSignature, $this->secretKey, true)
        );

        return hash_equals($expectedSignature, $signatureHeader);
    }

    /**
     * Generate the HMAC-SHA256 signature for outgoing requests.
     */
    protected function generateSignature(
        string $requestId,
        string $requestTimestamp,
        string $requestTarget,
        string $jsonBody
    ): string {
        $digest = base64_encode(hash('sha256', $jsonBody, true));

        $rawSignature = "Client-Id:{$this->clientId}\n"
            . "Request-Id:{$requestId}\n"
            . "Request-Timestamp:{$requestTimestamp}\n"
            . "Request-Target:{$requestTarget}\n"
            . "Digest:{$digest}";

        return 'HMACSHA256=' . base64_encode(
            hash_hmac('sha256', $rawSignature, $this->secretKey, true)
        );
    }
}
