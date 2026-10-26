<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SaleService
{
    /**
     * Execute atomic sale transaction with pessimistic locking and stock validation.
     *
     * @param  User  $creator  The authenticated user processing the sale (Admin/Staff/Customer)
     * @param  int|null  $customerId  Nullable registered customer ID
     * @param  array<array{product_id: int, quantity: int}>  $items
     * @param  string|null  $soldAt
     * @param  string  $paymentMethod
     * @param  string  $paymentStatus
     * @param  string|null  $paymentReference
     * @param  string|null  $paidAt
     * @return Sale
     *
     * @throws ValidationException
     */
    public function createSale(
        User $creator,
        ?int $customerId,
        array $items,
        ?string $soldAt = null,
        string $paymentMethod = 'qris',
        string $paymentStatus = 'paid',
        ?string $paymentReference = null,
        ?string $paidAt = null
    ): Sale
    {
        if (empty($items)) {
            throw ValidationException::withMessages([
                'items' => 'Minimal harus menambahkan satu produk untuk transaksi penjualan.',
            ]);
        }

        return DB::transaction(function () use ($creator, $customerId, $items, $soldAt, $paymentMethod, $paymentStatus, $paymentReference, $paidAt) {
            // 1. Generate unique invoice number: INV-YYYYMMDD-XXXX
            $datePrefix = now()->format('Ymd');
            do {
                $saleNumber = 'INV-' . $datePrefix . '-' . strtoupper(Str::random(4));
            } while (Sale::where('sale_number', $saleNumber)->exists());

            // Generate payment reference code if not provided
            if (! $paymentReference) {
                $paymentReference = 'PG-MID-' . $datePrefix . '-' . mt_rand(10000, 99999);
            }

            // 2. Create Sale Header
            $sale = Sale::create([
                'user_id' => $creator->id,
                'customer_id' => $customerId,
                'sale_number' => $saleNumber,
                'total' => 0, // Computed below
                'status' => 'completed',
                'sold_at' => $soldAt ? $soldAt : now(),
                'payment_method' => $paymentMethod ?: 'qris',
                'payment_status' => $paymentStatus ?: 'paid',
                'payment_reference' => $paymentReference,
                'paid_at' => ($paymentStatus === 'paid') ? ($paidAt ?: now()) : null,
            ]);

            $grandTotal = 0;

            // 3. Process items with pessimistic lock (lockForUpdate)
            foreach ($items as $itemData) {
                $productId = (int) $itemData['product_id'];
                $qty = (int) $itemData['quantity'];

                if ($qty < 1) {
                    throw ValidationException::withMessages([
                        'items' => "Kuantitas pembelian harus minimal 1 unit.",
                    ]);
                }

                /** @var Product $product */
                $product = Product::lockForUpdate()->find($productId);

                if (! $product) {
                    throw ValidationException::withMessages([
                        'items' => "Produk yang dipilih tidak ditemukan dalam katalog sistem.",
                    ]);
                }

                // Strict stock validation
                if ($product->stock < $qty) {
                    throw ValidationException::withMessages([
                        'items' => "Stok produk '{$product->name}' (SKU: {$product->sku}) tidak mencukupi! Tersedia: {$product->stock} unit, diminta: {$qty} unit.",
                    ]);
                }

                // Take real selling price directly from product record in database
                $unitPrice = (float) $product->selling_price;
                $subtotal = $qty * $unitPrice;

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'unit_price' => $unitPrice,
                    'subtotal' => $subtotal,
                ]);

                // Atomically decrement stock
                $product->decrement('stock', $qty);

                $grandTotal += $subtotal;
            }

            // 4. Update Header with computed total
            $sale->update(['total' => $grandTotal]);

            return $sale;
        });
    }

    /**
     * Cancel sale transaction and roll back product stocks (Admin Only).
     *
     * @param  Sale  $sale
     * @return Sale
     *
     * @throws Exception
     */
    public function cancelSale(Sale $sale): Sale
    {
        if ($sale->status !== 'completed') {
            throw new Exception("Hanya transaksi dengan status 'completed' yang dapat dibatalkan.");
        }

        return DB::transaction(function () use ($sale) {
            foreach ($sale->items as $item) {
                /** @var Product $product */
                $product = Product::lockForUpdate()->find($item->product_id);

                if ($product) {
                    // Restore stock back to warehouse
                    $product->increment('stock', $item->quantity);
                }
            }

            $sale->update([
                'status' => 'cancelled',
                'payment_status' => 'refunded',
            ]);

            return $sale;
        });
    }

    /**
     * Confirm / Simulate payment success from payment gateway.
     */
    public function confirmPayment(Sale $sale, ?string $reference = null): Sale
    {
        $sale->update([
            'payment_status' => 'paid',
            'payment_reference' => $reference ?: ($sale->payment_reference ?: ('PG-MID-' . now()->format('Ymd') . '-' . mt_rand(10000, 99999))),
            'paid_at' => now(),
        ]);

        return $sale;
    }
}
