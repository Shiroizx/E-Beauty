<?php

namespace App\Http\Requests;

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
            'product_id' => ['required', 'exists:products,id'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
            'images' => ['nullable', 'array', 'max:3'],
            'images.*' => ['image', 'mimes:jpeg,png,jpg', 'max:2048'], // 2MB max per image
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'product_id.required' => 'Produk harus dipilih',
            'product_id.exists' => 'Produk tidak ditemukan',
            'rating.required' => 'Rating harus diisi',
            'rating.min' => 'Rating minimal 1 bintang',
            'rating.max' => 'Rating maksimal 5 bintang',
            'comment.max' => 'Komentar maksimal 1000 karakter',
            'images.max' => 'Maksimal 3 gambar',
            'images.*.image' => 'File harus berupa gambar',
            'images.*.mimes' => 'Format gambar harus jpeg, png, atau jpg',
            'images.*.max' => 'Ukuran gambar maksimal 2MB',
        ];
    }
}
