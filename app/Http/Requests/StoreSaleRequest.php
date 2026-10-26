<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSaleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && ($this->user()->isAdmin() || $this->user()->isStaff());
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'customer_id' => ['nullable', 'exists:users,id'],
            'sold_at' => ['nullable', 'date'],
            'payment_method' => ['nullable', 'string', 'in:cash,qris,va_bca,va_bsi,va_mandiri,ewallet'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'distinct', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
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
            'customer_id.exists' => 'Data pelanggan yang dipilih tidak valid.',
            'items.required' => 'Minimal harus menambahkan satu produk pada keranjang transaksi kasir.',
            'items.min' => 'Minimal harus menambahkan satu produk pada transaksi.',
            'items.*.product_id.required' => 'Produk wajib dipilih.',
            'items.*.product_id.distinct' => 'Produk tidak boleh diduplikasi pada baris yang berbeda.',
            'items.*.product_id.exists' => 'Produk yang dipilih tidak ditemukan dalam database.',
            'items.*.quantity.required' => 'Kuantitas penjualan wajib diisi.',
            'items.*.quantity.integer' => 'Kuantitas penjualan harus berupa bilangan bulat.',
            'items.*.quantity.min' => 'Kuantitas minimal adalah 1 unit.',
        ];
    }
}
