<?php

namespace App\Http\Requests;

use App\Models\Order;
use App\Models\Review;
use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'order_number' => ['required', 'string', 'max:64'],
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
            'images' => ['nullable', 'array', 'max:3'],
            'images.*' => ['image', 'mimes:jpeg,jpg,png', 'max:3072'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $userId = (int) auth()->id();
            $productId = (int) $this->input('product_id');
            $orderNumber = (string) $this->input('order_number');

            $validOrder = Order::query()
                ->where('user_id', $userId)
                ->where('order_number', $orderNumber)
                ->where('status', 'completed')
                ->whereHas('items', fn ($q) => $q->where('product_id', $productId))
                ->exists();

            if (! $validOrder) {
                $validator->errors()->add(
                    'order_number',
                    'Ulasan hanya dapat dikirim untuk pesanan berstatus Selesai yang memuat produk ini.'
                );
            }

            if (Review::query()->where('user_id', $userId)->where('product_id', $productId)->exists()) {
                $validator->errors()->add('product_id', 'Anda sudah pernah mengirim ulasan untuk produk ini.');
            }
        });
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'order_number.required' => 'Nomor pesanan diperlukan',
            'product_id.required' => 'Produk harus dipilih',
            'product_id.exists' => 'Produk tidak ditemukan',
            'rating.required' => 'Rating harus diisi',
            'rating.min' => 'Rating minimal 1 bintang',
            'rating.max' => 'Rating maksimal 5 bintang',
            'comment.max' => 'Komentar maksimal 1000 karakter',
            'images.max' => 'Maksimal 3 gambar',
            'images.*.image' => 'Lampiran harus berupa gambar (bukan video atau dokumen lain).',
            'images.*.mimes' => 'Gunakan hanya gambar JPG, JPEG, atau PNG.',
            'images.*.max' => 'Ukuran per gambar maksimal 3 MB.',
        ];
    }
}
