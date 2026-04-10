<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Review;
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
            'order_id' => $data['order_id'] ?? null,
            'rating' => $data['rating'],
            'comment' => $data['comment'] ?? null,
            'images' => $imagePaths,
            'is_verified_purchase' => $data['is_verified_purchase'] ?? false,
            'is_approved' => $data['auto_approve'] ?? false,
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
            ->with(['product', 'user', 'order'])
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
     * Status ulasan untuk satu baris pesanan (UI).
     *
     * @return 'locked'|'open'|'pending'|'approved'
     */
    public function getReviewStateForOrderLine(Order $order, int $userId, int $productId): string
    {
        if ($order->status !== 'completed') {
            return 'locked';
        }

        $review = Review::query()
            ->where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if (! $review) {
            return 'open';
        }

        return $review->is_approved ? 'approved' : 'pending';
    }

    /**
     * Map product_id => state untuk semua item di pesanan (unik per produk).
     *
     * @return array<int, string>
     */
    public function getReviewStatesForOrder(Order $order, int $userId): array
    {
        $map = [];
        foreach ($order->items as $item) {
            $pid = (int) $item->product_id;
            if (! isset($map[$pid])) {
                $map[$pid] = $this->getReviewStateForOrderLine($order, $userId, $pid);
            }
        }

        return $map;
    }

    /**
     * User punya minimal satu pesanan selesai yang berisi produk ini.
     */
    public function userHasCompletedPurchaseOfProduct(int $userId, int $productId): bool
    {
        return Order::query()
            ->where('user_id', $userId)
            ->where('status', 'completed')
            ->whereHas('items', fn ($q) => $q->where('product_id', $productId))
            ->exists();
    }

    /**
     * Validasi pesanan untuk mengirim ulasan (pemilik, selesai, berisi produk).
     */
    public function findOrderEligibleForReview(int $userId, string $orderNumber, int $productId): ?Order
    {
        return Order::query()
            ->where('user_id', $userId)
            ->where('order_number', $orderNumber)
            ->where('status', 'completed')
            ->whereHas('items', fn ($q) => $q->where('product_id', $productId))
            ->first();
    }

    /**
     * Check if user can review product (setelah validasi pesanan di controller).
     */
    public function canUserReview(int $userId, int $productId): array
    {
        $exists = Review::where('user_id', $userId)
            ->where('product_id', $productId)
            ->exists();

        if ($exists) {
            return [
                'can_review' => false,
                'message' => 'Anda sudah pernah mengirim ulasan untuk produk ini.',
            ];
        }

        if (! $this->userHasCompletedPurchaseOfProduct($userId, $productId)) {
            return [
                'can_review' => false,
                'message' => 'Ulasan hanya dapat diberikan setelah pesanan Anda berstatus Selesai dan berisi produk ini.',
            ];
        }

        return [
            'can_review' => true,
            'message' => 'Anda dapat memberikan ulasan.',
        ];
    }

    /**
     * Teks singkat untuk PDP (form ulasan hanya dari halaman pesanan selesai).
     *
     * @return array{key: string, message: string}
     */
    public function productPageReviewHint(?int $userId, int $productId): array
    {
        if ($userId === null) {
            return [
                'key' => 'guest',
                'message' => 'Ulasan ditampilkan dari pembeli yang telah menyelesaikan pesanan.',
            ];
        }

        $review = Review::query()
            ->where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if ($review) {
            return [
                'key' => $review->is_approved ? 'approved' : 'pending',
                'message' => $review->is_approved
                    ? 'Terima kasih — ulasan Anda sudah ditampilkan.'
                    : 'Terima kasih — ulasan Anda telah kami terima.',
            ];
        }

        if ($this->userHasCompletedPurchaseOfProduct($userId, $productId)) {
            return [
                'key' => 'eligible',
                'message' => 'Tulis ulasan dari Pesanan Saya → buka detail pesanan berstatus Selesai.',
            ];
        }

        return [
            'key' => 'not_eligible',
            'message' => 'Setelah pesanan berstatus Selesai, Anda dapat memberi rating dan komentar dari halaman detail pesanan.',
        ];
    }
}
