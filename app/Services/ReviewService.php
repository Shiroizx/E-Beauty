<?php

namespace App\Services;

use App\Models\Review;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;

class ReviewService
{
    /**
     * Create a new review
     */
    public function createReview(array $data)
    {
        // Handle image uploads if present
        $imagePaths = [];
        if (!empty($data['images'])) {
            foreach ($data['images'] as $image) {
                $path = $image->store('reviews', 'public');
                $imagePaths[] = $path;
            }
        }

        $review = Review::create([
            'product_id' => $data['product_id'],
            'user_id' => $data['user_id'],
            'rating' => $data['rating'],
            'comment' => $data['comment'] ?? null,
            'images' => $imagePaths,
            'is_verified_purchase' => $data['is_verified_purchase'] ?? false,
            'is_approved' => $data['auto_approve'] ?? false, // Auto-approve or require moderation
        ]);

        return $review;
    }

    /**
     * Get reviews for a product
     */
    public function getProductReviews(int $productId, array $filters = [])
    {
        $query = Review::where('product_id', $productId)
            ->approved()
            ->with('user');

        // Filter by rating
        if (!empty($filters['rating'])) {
            $query->byRating($filters['rating']);
        }

        // Filter by verified purchase
        if (!empty($filters['verified_only'])) {
            $query->verifiedPurchase();
        }

        // Sort
        $sortBy = $filters['sort_by'] ?? 'newest';
        
        switch ($sortBy) {
            case 'highest_rating':
                $query->orderByDesc('rating');
                break;
            case 'lowest_rating':
                $query->orderBy('rating');
                break;
            case 'newest':
            default:
                $query->orderByDesc('created_at');
                break;
        }

        return $query->paginate($filters['per_page'] ?? 10);
    }

    /**
     * Get pending reviews (for admin moderation)
     */
    public function getPendingReviews()
    {
        return Review::pending()
            ->with(['product', 'user'])
            ->orderByDesc('created_at')
            ->paginate(20);
    }

    /**
     * Approve a review
     */
    public function approveReview(int $reviewId)
    {
        $review = Review::findOrFail($reviewId);
        $review->approve();

        return $review;
    }

    /**
     * Reject/Delete a review
     */
    public function rejectReview(int $reviewId)
    {
        $review = Review::findOrFail($reviewId);
        
        // Delete associated images
        if ($review->images) {
            foreach ($review->images as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        $review->delete();

        return true;
    }

    /**
     * Get review statistics
     */
    public function getStatistics()
    {
        return [
            'total_reviews' => Review::count(),
            'approved_reviews' => Review::approved()->count(),
            'pending_reviews' => Review::pending()->count(),
            'verified_purchase_reviews' => Review::verifiedPurchase()->count(),
            'average_rating' => round(Review::approved()->avg('rating'), 2),
        ];
    }

    /**
     * Get product rating breakdown
     */
    public function getProductRatingBreakdown(int $productId)
    {
        $reviews = Review::where('product_id', $productId)
            ->approved()
            ->get();

        $total = $reviews->count();

        $breakdown = [
            '5' => 0,
            '4' => 0,
            '3' => 0,
            '2' => 0,
            '1' => 0,
        ];

        foreach ($reviews as $review) {
            $breakdown[(string)$review->rating]++;
        }

        // Calculate percentages
        foreach ($breakdown as $rating => $count) {
            $breakdown[$rating] = [
                'count' => $count,
                'percentage' => $total > 0 ? round(($count / $total) * 100, 1) : 0,
            ];
        }

        return [
            'total' => $total,
            'average' => $total > 0 ? round($reviews->avg('rating'), 1) : 0,
            'breakdown' => $breakdown,
        ];
    }

    /**
     * Check if user can review product
     */
    public function canUserReview(int $userId, int $productId)
    {
        // Check if user already reviewed this product
        $existingReview = Review::where('user_id', $userId)
            ->where('product_id', $productId)
            ->exists();

        if ($existingReview) {
            return [
                'can_review' => false,
                'message' => 'Anda sudah memberikan review untuk produk ini',
            ];
        }

        // In a real application, you might also check if user purchased the product

        return [
            'can_review' => true,
            'message' => 'Anda dapat memberikan review',
        ];
    }
}
