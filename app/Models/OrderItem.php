<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_variant_id',
        'product_name',
        'variant_sku',
        'variant_attributes_summary',
        'thumbnail_path',
        'quantity',
        'unit_price',
        'subtotal',
        'tax_amount',
        'total',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function getSkuAttribute(): ?string
    {
        return $this->variant_sku;
    }

    public function getVariantTitleAttribute(): ?string
    {
        return $this->variant_attributes_summary;
    }

    public function getHsnCodeAttribute(): string
    {
        $catName = strtolower($this->product?->categories?->first()?->name ?? '');
        if (str_contains($catName, 'audio') || str_contains($catName, 'acoustic')) {
            return '8518.30';
        } elseif (str_contains($catName, 'horology') || str_contains($catName, 'wearable')) {
            return '9102.12';
        } elseif (str_contains($catName, 'desk') || str_contains($catName, 'keyboard')) {
            return '8471.60';
        } elseif (str_contains($catName, 'optic') || str_contains($catName, 'camera')) {
            return '9006.59';
        } elseif (str_contains($catName, 'power') || str_contains($catName, 'charg')) {
            return '8504.40';
        }
        return '8517.62';
    }
}
