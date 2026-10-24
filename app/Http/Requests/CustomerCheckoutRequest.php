<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerCheckoutRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isCustomer();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
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
            'items.required' => 'Keranjang belanja Anda masih kosong. Silakan pilih minimal satu produk.',
            'items.min' => 'Keranjang belanja Anda masih kosong.',
            'items.*.product_id.required' => 'Produk wajib dipilih.',
            'items.*.quantity.required' => 'Kuantitas pesanan wajib diisi.',
            'items.*.quantity.integer' => 'Kuantitas pesanan harus berupa bilangan bulat.',
            'items.*.quantity.min' => 'Kuantitas pesanan minimal 1 unit.',
        ];
    }
}
