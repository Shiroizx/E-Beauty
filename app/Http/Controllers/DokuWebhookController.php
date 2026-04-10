<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\DokuService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DokuWebhookController extends Controller
{
    public function __construct(
        protected DokuService $dokuService
    ) {}

    /**
     * Handle DOKU payment notification (webhook).
     *
     * DOKU sends POST requests to this endpoint when payment status changes.
     */
    public function handleNotification(Request $request)
    {
        $rawBody = $request->getContent();

        // Extract headers
        $signatureHeader  = $request->header('Signature', '');
        $clientId         = $request->header('Client-Id', '');
        $requestId        = $request->header('Request-Id', '');
        $requestTimestamp  = $request->header('Request-Timestamp', '');
        $requestTarget    = '/doku/notification';

        Log::info('DOKU Notification Received', [
            'request_id'     => $requestId,
            'content_length' => strlen($rawBody),
        ]);

        // Verify signature
        $isValid = $this->dokuService->verifyNotificationSignature(
            $signatureHeader,
            $requestId,
            $requestTimestamp,
            $requestTarget,
            $rawBody
        );

        if (! $isValid) {
            Log::warning('DOKU Notification: Invalid signature', [
                'request_id'       => $requestId,
                'signature_header' => $signatureHeader,
            ]);

            return response()->json(['message' => 'Invalid signature'], 401);
        }

        // Parse the notification body
        $data = json_decode($rawBody, true);

        if (! $data) {
            Log::warning('DOKU Notification: Invalid JSON body');
            return response()->json(['message' => 'Invalid body'], 400);
        }

        $invoiceNumber   = $data['order']['invoice_number'] ?? null;
        $transactionStatus = $data['transaction']['status'] ?? null;

        if (! $invoiceNumber) {
            Log::warning('DOKU Notification: No invoice number in payload');
            return response()->json(['message' => 'No invoice number'], 400);
        }

        // Find order by invoice number (we use order_number as invoice)
        $order = Order::query()
            ->where('order_number', $invoiceNumber)
            ->where('payment_method', 'doku')
            ->first();

        if (! $order) {
            Log::warning('DOKU Notification: Order not found', [
                'invoice_number' => $invoiceNumber,
            ]);

            return response()->json(['message' => 'Order not found'], 404);
        }

        // Process based on transaction status
        $this->processPaymentStatus($order, $transactionStatus, $data);

        return response()->json(['message' => 'OK'], 200);
    }

    /**
     * Process the payment status from DOKU notification.
     */
    protected function processPaymentStatus(Order $order, ?string $status, array $data): void
    {
        Log::info('DOKU Payment Status Processing', [
            'order_number' => $order->order_number,
            'status'       => $status,
        ]);

        $channelId = $data['channel']['id'] ?? 'doku';

        switch (strtoupper($status ?? '')) {
            case 'SUCCESS':
                $order->update([
                    'payment_status' => 'paid',
                    'status'         => 'processing',
                    'payment_method' => strtolower($channelId) === 'doku' ? 'doku' : $channelId,
                ]);
                Log::info('DOKU Payment SUCCESS', ['order' => $order->order_number, 'channel' => $channelId]);
                break;

            case 'FAILED':
                $order->update([
                    'payment_status' => 'failed',
                    'status'         => 'cancelled',
                    'payment_method' => strtolower($channelId) === 'doku' ? 'doku' : $channelId,
                ]);
                Log::info('DOKU Payment FAILED', ['order' => $order->order_number, 'channel' => $channelId]);
                break;

            case 'EXPIRED':
                $order->update([
                    'payment_status' => 'expired',
                    'status'         => 'cancelled',
                ]);
                Log::info('DOKU Payment EXPIRED', ['order' => $order->order_number]);
                break;

            default:
                Log::info('DOKU Unhandled status', ['status' => $status, 'order' => $order->order_number]);
                break;
        }
    }
}
