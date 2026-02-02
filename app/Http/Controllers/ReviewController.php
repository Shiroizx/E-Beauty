<?php

namespace App\Http\Controllers;

use App\Services\ReviewService;
use App\Http\Requests\StoreReviewRequest;
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
        // Check if user can review this product
        $canReview = $this->reviewService->canUserReview(
            auth()->id(),
            $request->product_id
        );

        if (!$canReview['can_review']) {
            return back()->with('error', $canReview['message']);
        }

        $data = $request->validated();
        $data['user_id'] = auth()->id();
        $data['auto_approve'] = false; // Require moderation

        try {
            $review = $this->reviewService->createReview($data);

            return back()->with('success', 'Review Anda berhasil dikirim dan menunggu persetujuan admin');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat mengirim review');
        }
    }
}
