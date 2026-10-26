<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'customer_id',
        'sale_number',
        'total',
        'status',
        'sold_at',
        'payment_method',
        'payment_status',
        'payment_reference',
        'paid_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'total' => 'decimal:2',
            'sold_at' => 'datetime',
            'paid_at' => 'datetime',
        ];
    }

    /**
     * Get human-friendly label for payment method.
     */
    public function getPaymentMethodLabelAttribute(): string
    {
        return match ($this->payment_method) {
            'qris' => 'QRIS Instant (All Payment)',
            'va_bca' => 'BCA Virtual Account',
            'va_bsi' => 'BSI Virtual Account',
            'va_mandiri' => 'Mandiri Bill / VA',
            'ewallet' => 'E-Wallet (GoPay/OVO/DANA)',
            'cash' => 'Tunai / Cash di Toko',
            default => strtoupper((string) $this->payment_method),
        };
    }

    /**
     * Generate simulated virtual account number.
     */
    public function getVirtualAccountNumberAttribute(): string
    {
        $bankCode = match ($this->payment_method) {
            'va_bca' => '80777',
            'va_bsi' => '90012',
            'va_mandiri' => '88901',
            default => '70012',
        };

        $phoneSuffix = preg_replace('/[^0-9]/', '', (string) ($this->customer->phone ?? '08123456789'));
        if (strlen($phoneSuffix) > 10) {
            $phoneSuffix = substr($phoneSuffix, -10);
        } else {
            $phoneSuffix = str_pad($phoneSuffix, 10, '0', STR_PAD_LEFT);
        }

        return $bankCode . ' ' . substr($phoneSuffix, 0, 4) . ' ' . substr($phoneSuffix, 4);
    }

    /**
     * User (staff/admin or self-service customer) who processed this sale.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Customer (if registered) associated with this sale.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    /**
     * Sale items included in this sale.
     */
    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class, 'sale_id');
    }
}
