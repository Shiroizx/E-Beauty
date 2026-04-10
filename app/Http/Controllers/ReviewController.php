<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReviewRequest;
use App\Models\Order;
use App\Services\ReviewService;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    protected $reviewService;

    public function __construct(ReviewService $reviewService)
    {
        $this->middleware('auth')->only(['store']);
        $this->reviewService = $reviewService;
    }

    /**
     * Get reviews for a product (AJAX)
     */
    public function index(Request $request)
    {
        $productId = $request->input('product_id');
        $filters = [
            'rating' => $request->input('rating'),
            'verified_only' => $request->boolean('verified_only'),
            'sort_by' => $request->input('sort_by', 'newest'),
            'per_page' => $request->input('per_page', 10),
        ];

        $reviews = $this->reviewService->getProductReviews($productId, $filters);

        return response()->json($reviews);
    }

    /**
     * Store a new review
     */
    public function store(StoreReviewRequest $request)
    {
        $data = $request->validated();

        $order = Order::query()
            ->where('user_id', auth()->id())
            ->where('order_number', $data['order_number'])
            ->firstOrFail();

        $data['user_id'] = auth()->id();
        $data['order_id'] = $order->id;
        $data['is_verified_purchase'] = true;
        $data['auto_approve'] = false;
        unset($data['order_number']);

        try {
            $this->reviewService->createReview($data);

            return redirect()
                ->route('orders.show', $order->order_number)
                ->with('success', 'Terima kasih, ulasan Anda telah terkirim.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat mengirim ulasan. Silakan coba lagi.');
        }
    }
}
