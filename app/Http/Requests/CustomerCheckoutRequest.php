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
        return ! auth()->check() || auth()->user()->isCustomer();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $hasAuth = auth()->check();

        return [
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'distinct', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'payment_method' => ['nullable', 'string', 'in:qris,va_bca,va_bsi,va_mandiri,ewallet'],
            'customer_name' => [$hasAuth ? 'nullable' : 'required_without:name', 'string', 'max:150'],
            'name' => ['nullable', 'string', 'max:150'],
            'customer_phone' => ['nullable', 'string', 'max:30'],
            'phone' => ['nullable', 'string', 'max:30'],
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
            'customer_name.required_without' => 'Nama Pelanggan wajib diisi untuk memproses pesanan.',
        ];
    }
}
