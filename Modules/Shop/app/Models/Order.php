<?php

namespace Modules\Shop\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Shop\Models\Concerns\BelongsToShop;

/** A sale: POS (sale_type "pos") or online order. total_amount is before discount. */
class Order extends Model
{
    use BelongsToShop;

    public const STATUSES = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
    /** Counter (POS) payment methods. */
    public const POS_PAYMENT_METHODS = ['cash' => 'Cash', 'card' => 'Card', 'esewa' => 'eSewa', 'khalti' => 'Khalti', 'bank' => 'Bank Transfer'];
    /** Storefront (online) payment methods. */
    public const ONLINE_PAYMENT_METHODS = ['cash_on_delivery' => 'Cash on delivery', 'khalti' => 'Khalti (online)', 'khalti_qr' => 'Khalti QR', 'esewa_qr' => 'eSewa QR'];
    public const PAYMENT_METHODS = self::POS_PAYMENT_METHODS + self::ONLINE_PAYMENT_METHODS;

    protected $connection = 'shop';

    protected $fillable = [
        'company_id', 'order_number', 'user_id', 'customer_id', 'full_name', 'phone_number', 'email',
        'address', 'address_detail', 'latitude', 'longitude',
        'total_amount', 'discount', 'tax_amount', 'paid_amount',
        'order_status', 'payment_method', 'sale_type', 'billing_details',
        'payment_status', 'paid_at',
    ];

    protected $casts = [
        'billing_details' => 'array',
        'paid_at'         => 'datetime',
        'total_amount'    => 'decimal:2',
        'discount'        => 'decimal:2',
        'tax_amount'      => 'decimal:2',
        'paid_amount'     => 'decimal:2',
    ];

    public function orderProducts(): HasMany { return $this->hasMany(OrderProduct::class); }
    public function orderProduces(): HasMany { return $this->orderProducts(); } // name used by the storefront theme
    public function customer(): BelongsTo { return $this->belongsTo(Customer::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }

    public function getOrderNumberFormatAttribute(): string { return 'ODR-'.$this->order_number; }
    public function getGrandTotalAttribute(): float { return max(0, (float) $this->total_amount - (float) $this->discount); }
    public function getChangeAmountAttribute(): float { return $this->paid_amount ? max(0, (float) $this->paid_amount - $this->grand_total) : 0; }
    public function getDueAmountAttribute(): float { return max(0, $this->grand_total - (float) $this->paid_amount); }
    public function getPaymentMethodLabelAttribute(): string
    {
        if ($this->sale_type === 'online' && isset(self::ONLINE_PAYMENT_METHODS[$this->payment_method])) {
            return self::ONLINE_PAYMENT_METHODS[$this->payment_method];
        }

        return self::PAYMENT_METHODS[$this->payment_method] ?? ucfirst((string) $this->payment_method);
    }

    /** Payment screenshot uploaded with a QR payment (online orders). */
    public function getPaymentScreenshotUrlAttribute(): ?string
    {
        $path = $this->billing_details['screenshot'] ?? null;

        return $path ? \Illuminate\Support\Facades\Storage::disk('public')->url($path) : null;
    }

    public static function newNumber(): string
    {
        do {
            $number = date('Ymd').'-'.str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT);
        } while (static::withoutGlobalScopes()->where('order_number', $number)->exists());

        return $number;
    }
}
