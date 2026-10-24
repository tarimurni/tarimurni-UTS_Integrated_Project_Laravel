<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePurchaseRequest extends FormRequest
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
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'purchased_at' => ['nullable', 'date'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'distinct', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_cost' => ['required', 'numeric', 'min:0'],
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
            'supplier_id.required' => 'Supplier / distributor wajib dipilih.',
            'supplier_id.exists' => 'Supplier yang dipilih tidak valid.',
            'items.required' => 'Minimal harus menambahkan satu produk dalam pembelian.',
            'items.min' => 'Minimal harus menambahkan satu produk dalam pembelian.',
            'items.*.product_id.required' => 'Produk pada setiap baris wajib dipilih.',
            'items.*.product_id.distinct' => 'Produk tidak boleh duplikat pada baris yang berbeda.',
            'items.*.product_id.exists' => 'Produk yang dipilih tidak ditemukan.',
            'items.*.quantity.required' => 'Jumlah kuantitas produk wajib diisi.',
            'items.*.quantity.integer' => 'Kuantitas harus berupa bilangan bulat.',
            'items.*.quantity.min' => 'Kuantitas minimal adalah 1.',
            'items.*.unit_cost.required' => 'Harga beli satuan wajib diisi.',
            'items.*.unit_cost.numeric' => 'Harga beli satuan harus berupa angka.',
            'items.*.unit_cost.min' => 'Harga beli satuan tidak boleh negatif.',
        ];
    }
}
