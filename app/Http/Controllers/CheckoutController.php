<?php

namespace App\Http\Controllers;

use App\Exceptions\CheckoutException;
use App\Models\Order;
use App\Services\CartService;
use App\Services\CheckoutService;
use App\Services\PromoService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CheckoutController extends Controller
{
    private const WIZARD_KEY = 'checkout_wizard';

    public function __construct(
        protected CartService $cartService,
        protected CheckoutService $checkoutService,
        protected PromoService $promoService
    ) {}

    public function index()
    {
        return redirect()->route('checkout.step', ['step' => 1]);
    }

    public function show(Request $request, int $step)
    {
        if ($step < 1 || $step > 3) {
            abort(404);
        }

        $lines = $this->cartService->getSelectedLineItems();
        if ($lines->isEmpty()) {
            $request->session()->forget(self::WIZARD_KEY);

            return redirect()
                ->route('cart.index')
                ->with('error', 'Tidak ada produk yang dipilih. Pilih produk di keranjang sebelum checkout.');
        }

        $wizard = $request->session()->get(self::WIZARD_KEY, []);

        if ($step >= 2 && empty($wizard['biodata'])) {
            return redirect()
                ->route('checkout.step', ['step' => 1])
                ->with('error', 'Lengkapi biodata terlebih dahulu.');
        }

        if ($step >= 3 && empty($wizard['shipping'])) {
            return redirect()
                ->route('checkout.step', ['step' => 2])
                ->with('error', 'Lengkapi alamat pengiriman terlebih dahulu.');
        }

        $totals = $this->computeTotals($lines, $wizard);

        return view('checkout.wizard', array_merge(compact('step', 'lines', 'wizard'), $totals));
    }

    public function storeStep1(Request $request)
    {
        if ($this->cartService->getSelectedLineItems()->isEmpty()) {
            $request->session()->forget(self::WIZARD_KEY);

            return redirect()->route('cart.index')->with('error', 'Tidak ada produk yang dipilih.');
        }

        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', Rule::in([$request->user()->email])],
            'phone' => ['required', 'string', 'max:20', 'regex:/^[0-9+\-\s()]+$/'],
        ], [
            'phone.regex' => 'Nomor telepon hanya boleh angka, +, spasi, tanda kurung, atau tanda hubung.',
            'email.in' => 'Email harus sama dengan akun Anda.',
        ]);

        $wizard = $request->session()->get(self::WIZARD_KEY, []);
        $wizard['biodata'] = $data;
        $request->session()->put(self::WIZARD_KEY, $wizard);

        return redirect()
            ->route('checkout.step', ['step' => 2])
            ->with('success', 'Biodata tersimpan. Lanjutkan ke alamat pengiriman.');
    }

    public function storeStep2(Request $request)
    {
        if ($this->cartService->getSelectedLineItems()->isEmpty()) {
            $request->session()->forget(self::WIZARD_KEY);

            return redirect()->route('cart.index')->with('error', 'Tidak ada produk yang dipilih.');
        }

        $wizard = $request->session()->get(self::WIZARD_KEY, []);
        if (empty($wizard['biodata'])) {
            return redirect()
                ->route('checkout.step', ['step' => 1])
                ->with('error', 'Lengkapi biodata terlebih dahulu.');
        }

        $data = $request->validate([
            'recipient_name' => ['nullable', 'string', 'max:120'],
            'shipping_address_line' => ['required', 'string', 'max:255'],
            'shipping_province' => ['required', 'string', 'max:100'],
            'shipping_city' => ['required', 'string', 'max:100'],
            'shipping_district' => ['required', 'string', 'max:100'],
            'shipping_subdistrict' => ['required', 'string', 'max:100'],
            'shipping_postal_code' => ['required', 'string', 'max:16', 'regex:/^[0-9A-Za-z\s\-]+$/'],
            'shipping_service' => ['required', 'string', 'in:reguler,instant,kargo'],
            'shipping_courier' => ['required', 'string', 'max:50'],
            'customer_notes' => ['nullable', 'string', 'max:500'],
        ], [
            'shipping_postal_code.regex' => 'Kode pos tidak valid.',
            'shipping_service.required' => 'Pilih layanan pengiriman.',
            'shipping_courier.required' => 'Pilih kurir pengiriman.',
        ]);

        // Validate if service and courier combination is actually valid
        $lines = $this->cartService->getSelectedLineItems();
        $totalItems = $lines->sum('quantity');
        $shippingCost = $this->checkoutService->validateShippingSelection(
            $data['shipping_service'],
            $data['shipping_courier'],
            $data['shipping_province'],
            $data['shipping_city'],
            $totalItems
        );

        if ($shippingCost === null) {
            return back()->with('error', 'Pilihan layanan atau kurir tidak valid untuk alamat Anda.');
        }

        $wizard['shipping'] = $data;
        $request->session()->put(self::WIZARD_KEY, $wizard);

        return redirect()
            ->route('checkout.step', ['step' => 3])
            ->with('success', 'Alamat tersimpan. Pilih metode pembayaran.');
    }

    public function storeStep3(Request $request)
    {
        if ($this->cartService->getSelectedLineItems()->isEmpty()) {
            $request->session()->forget(self::WIZARD_KEY);

            return redirect()->route('cart.index')->with('error', 'Tidak ada produk yang dipilih.');
        }

        $wizard = $request->session()->get(self::WIZARD_KEY, []);
        if (empty($wizard['biodata']) || empty($wizard['shipping'])) {
            $step = empty($wizard['biodata']) ? 1 : 2;

            return redirect()
                ->route('checkout.step', ['step' => $step])
                ->with('error', 'Lengkapi langkah sebelumnya terlebih dahulu.');
        }

        $request->validate([
            'payment_method' => ['required', 'in:bank_transfer,cod,simulated_card,doku'],
        ]);

        $payload = $this->buildOrderPayload($wizard);

        $promoCode = $request->session()->get('checkout_promo_code');

        try {
            $result = $this->checkoutService->placeOrder(
                $request->user(),
                $payload,
                $request->input('payment_method'),
                is_string($promoCode) ? $promoCode : null
            );
        } catch (CheckoutException $e) {
            return redirect()
                ->route('checkout.step', ['step' => 3])
                ->with('error', $e->getMessage());
        }

        $request->session()->forget('checkout_promo_code');
        $request->session()->forget(self::WIZARD_KEY);
        $order = $result['order'];

        // If DOKU payment, redirect to DOKU payment page
        if ($order->isDokuPayment() && $order->doku_payment_url) {
            return redirect()->away($order->doku_payment_url);
        }

        return redirect()
            ->route('orders.confirmation', $order->order_number)
            ->with('success', 'Pesanan berhasil dibuat.');
    }

    public function confirmation(string $orderNumber)
    {
        $order = Order::query()
            ->where('order_number', $orderNumber)
            ->where('user_id', auth()->id())
            ->with('items.product')
            ->firstOrFail();

        return view('checkout.confirmation', [
            'order' => $order,
            'bank' => config('checkout.bank'),
        ]);
    }

    public function status(string $orderNumber)
    {
        $order = Order::query()
            ->where('order_number', $orderNumber)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        return response()->json([
            'payment_status' => $order->payment_status,
        ]);
    }

    /**
     * @param  array<string, mixed>  $wizard
     * @return array<string, mixed>
     */
    protected function buildOrderPayload(array $wizard): array
    {
        $b = $wizard['biodata'];
        $s = $wizard['shipping'];
        $recipient = trim((string) ($s['recipient_name'] ?? ''));

        return [
            'shipping_name' => $recipient !== '' ? $recipient : $b['full_name'],
            'shipping_phone' => $b['phone'],
            'shipping_address_line' => $s['shipping_address_line'],
            'shipping_province' => $s['shipping_province'],
            'shipping_city' => $s['shipping_city'],
            'shipping_district' => $s['shipping_district'],
            'shipping_subdistrict' => $s['shipping_subdistrict'],
            'shipping_postal_code' => $s['shipping_postal_code'],
            'shipping_service' => $s['shipping_service'],
            'shipping_courier' => $s['shipping_courier'],
            'customer_notes' => $s['customer_notes'] ?? null,
        ];
    }

    protected function computeTotals($lines, array $wizard = []): array
    {
        $subtotal = (float) $lines->sum(fn ($line) => $line->product->final_price * $line->quantity);

        $freeShippingAt = (float) config('checkout.free_shipping_subtotal', 500_000);
        $freeShippingBySubtotal = $subtotal >= $freeShippingAt;

        $shippingCost = 0.0;
        $shippingKnown = false;

        if ($freeShippingBySubtotal) {
            $shippingCost = 0.0;
            $shippingKnown = true;
        } elseif (! empty($wizard['shipping']['shipping_service'])) {
            $totalItems = $lines->sum('quantity');
            $cost = $this->checkoutService->validateShippingSelection(
                $wizard['shipping']['shipping_service'],
                $wizard['shipping']['shipping_courier'],
                $wizard['shipping']['shipping_province'],
                $wizard['shipping']['shipping_city'],
                $totalItems
            );
            if ($cost !== null) {
                $shippingCost = $cost;
                $shippingKnown = true;
            }
        }

        $promoDiscount = 0.0;
        $checkoutPromoCode = session('checkout_promo_code');
        $promoApplyError = null;
        $productIds = $lines->pluck('product_id')->unique()->values()->all();
        if (is_string($checkoutPromoCode) && $checkoutPromoCode !== '' && auth()->check()) {
            $validation = $this->promoService->validatePromoCode(
                strtoupper(trim($checkoutPromoCode)),
                auth()->id(),
                $subtotal,
                $productIds
            );
            if ($validation['valid']) {
                $promoDiscount = (float) $validation['discount_amount'];
            } else {
                $promoApplyError = $validation['message'];
            }
        }

        $total = round(max(0, $subtotal - $promoDiscount) + $shippingCost, 2);
        $amountToFreeShipping = max(0, $freeShippingAt - $subtotal);

        return compact(
            'subtotal',
            'shippingCost',
            'shippingKnown',
            'total',
            'freeShippingAt',
            'amountToFreeShipping',
            'promoDiscount',
            'checkoutPromoCode',
            'promoApplyError'
        );
    }

    public function applyPromo(Request $request)
    {
        $lines = $this->cartService->getSelectedLineItems();
        if ($lines->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang kosong.');
        }

        $wizard = $request->session()->get(self::WIZARD_KEY, []);
        if (empty($wizard['shipping'])) {
            return redirect()
                ->route('checkout.step', ['step' => 2])
                ->with('error', 'Lengkapi alamat pengiriman sebelum memakai promo.');
        }

        $data = $request->validate([
            'promo_code' => ['required', 'string', 'max:50'],
        ]);

        $subtotal = (float) $lines->sum(fn ($line) => $line->product->final_price * $line->quantity);
        $productIds = $lines->pluck('product_id')->unique()->values()->all();
        $code = strtoupper(trim($data['promo_code']));

        $validation = $this->promoService->validatePromoCode(
            $code,
            $request->user()->id,
            $subtotal,
            $productIds
        );

        if (! $validation['valid']) {
            return back()->withErrors(['promo_code' => $validation['message']])->withInput();
        }

        $request->session()->put('checkout_promo_code', $validation['promo']->code);

        return back()->with('success', $validation['message'] ?? 'Promo diterapkan.');
    }

    public function clearPromo(Request $request)
    {
        $request->session()->forget('checkout_promo_code');

        return back()->with('success', 'Kode promo dihapus.');
    }

    public function calculateShipping(Request $request)
    {
        $request->validate([
            'province' => 'required|string',
            'city' => 'required|string',
        ]);

        $lines = $this->cartService->getSelectedLineItems();
        $totalItems = $lines->sum('quantity');

        $rates = $this->checkoutService->calculateShippingRates(
            $request->input('province'),
            $request->input('city'),
            $totalItems
        );

        return response()->json([
            'success' => true,
            'rates' => $rates,
            'total_items' => $totalItems
        ]);
    }
}
