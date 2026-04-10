<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ReviewService;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    protected $reviewService;

    public function __construct(ReviewService $reviewService)
    {
        $this->reviewService = $reviewService;
    }

    /**
     * Display all reviews
     */
    public function index(Request $request)
    {
        $query = Review::with(['product', 'user', 'order']);

        // Filter by status
        $status = $request->input('status', 'all');
        if ($status === 'pending') {
            $query->pending();
        } elseif ($status === 'approved') {
            $query->approved();
        }

        // Search
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->whereHas('product', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })->orWhereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $reviews = $query->latest()->paginate(20);

        return view('admin.reviews.index', compact('reviews', 'status'));
    }

    /**
     * Detail satu review (moderasi).
     */
    public function show(Review $review)
    {
        $review->load(['product', 'user', 'order']);

        return view('admin.reviews.show', compact('review'));
    }

    /**
     * Approve review
     */
    public function approve(Review $review)
    {
        $this->reviewService->approveReview($review->id);

        return back()->with('success', 'Review berhasil disetujui');
    }

    /**
     * Delete/reject review
     */
    public function destroy(Review $review)
    {
        $this->reviewService->rejectReview($review->id);

        return back()->with('success', 'Review berhasil dihapus');
    }
}
