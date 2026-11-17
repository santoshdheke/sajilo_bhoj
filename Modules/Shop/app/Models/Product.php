<?php

namespace Modules\Shop\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\Shop\Models\Concerns\BelongsToShop;

/**
 * Stock is not stored: stock in = purchased quantity, stock out = sold quantity
 * (same as multiple_pasal). Eager-load purchaseOrderItems + stockOutLines for lists. Cancelled orders don't count as stock out.
 */
class Product extends Model
{
    use BelongsToShop;

    protected $connection = 'shop';

    protected $fillable = [
        'company_id', 'name', 'slug', 'sku', 'barcode', 'description', 'long_description',
        'price', 'cost_price', 'tax_rate', 'category_id', 'brand_id',
        'expiry_date', 'minimum_stock', 'unit',
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'price'       => 'decimal:2',
        'cost_price'  => 'decimal:2',
        'tax_rate'    => 'decimal:2',
    ];

    public function category(): BelongsTo { return $this->belongsTo(Category::class); }
    public function brand(): BelongsTo { return $this->belongsTo(Brand::class); }
    public function productImage(): HasOne { return $this->hasOne(ProductImage::class); }
    public function productImages(): HasMany { return $this->hasMany(ProductImage::class); }
    public function productTags(): HasMany { return $this->hasMany(ProductTag::class); }
    public function sections(): \Illuminate\Database\Eloquent\Relations\MorphToMany { return $this->morphToMany(Section::class, 'sectionable', 'section_items')->withTimestamps(); }
    public function productAttributes(): HasMany { return $this->hasMany(ProductAttribute::class); }
    public function productPricePerOrders(): HasMany { return $this->hasMany(ProductPrice::class)->where('price_type', 'price_per_order'); }
    public function purchaseOrderItems(): HasMany { return $this->hasMany(PurchaseOrderItem::class); }
    public function orderProducts(): HasMany { return $this->hasMany(OrderProduct::class); }
    // Sale lines that take stock out: cancelled orders give their items back.
    public function stockOutLines(): HasMany { return $this->hasMany(OrderProduct::class)->whereHas('order', fn ($q) => $q->where('order_status', '!=', 'cancelled')); }

    public function getImageUrlAttribute(): ?string { return $this->productImage?->image_url; }
    public function getImagePathAttribute(): string { return (string) $this->image_url; } // storefront theme name

    // Uses the loaded relation when present (lists eager-load it), else a SUM query.
    public function getStockInAttribute(): int
    {
        return (int) ($this->relationLoaded('purchaseOrderItems') ? $this->purchaseOrderItems->sum('quantity') : $this->purchaseOrderItems()->sum('quantity'));
    }

    public function getStockOutAttribute(): int
    {
        return (int) ($this->relationLoaded('stockOutLines') ? $this->stockOutLines->sum('quantity') : $this->stockOutLines()->sum('quantity'));
    }

    public function getCurrentStockAttribute(): int { return max(0, $this->stock_in - $this->stock_out); }
    public function getIsLowStockAttribute(): bool { return $this->stock_in > 0 && $this->current_stock <= $this->minimum_stock; }
    public function getIsExpiredAttribute(): bool { return $this->expiry_date && $this->expiry_date->isPast(); }
    public function getIsExpiringSoonAttribute(): bool { return $this->expiry_date && ! $this->is_expired && $this->expiry_date->diffInDays(now(), true) <= 30; }

    public function getProfitMarginAttribute(): ?float
    {
        return ($this->cost_price > 0 && $this->price > 0) ? round((($this->price - $this->cost_price) / $this->price) * 100, 1) : null;
    }

    public function getTaxAmountAttribute(): float { return round((float) $this->price * (float) $this->tax_rate / 100, 2); }
    public function getPriceWithTaxAttribute(): float { return (float) $this->price + $this->tax_amount; }
}
