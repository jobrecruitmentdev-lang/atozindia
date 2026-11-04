<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'sku',
        'barcode',
        'price_override',
        'sale_price_override',
        'cost_price_override',
        'weight_override',
        'dimensions_override',
        'status',
    ];

    protected $casts = [
        'price_override' => 'decimal:2',
        'sale_price_override' => 'decimal:2',
        'cost_price_override' => 'decimal:2',
        'weight_override' => 'decimal:2',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function attributeValues(): BelongsToMany
    {
        return $this->belongsToMany(AttributeValue::class, 'variant_attribute_values');
    }

    public function inventory(): HasOne
    {
        return $this->hasOne(Inventory::class);
    }

    public function getEffectivePriceAttribute(): float
    {
        if ($this->sale_price_override !== null && $this->sale_price_override > 0) {
            return (float) $this->sale_price_override;
        }

        if ($this->price_override !== null) {
            return (float) $this->price_override;
        }

        return (float) $this->product->effective_price;
    }

    public function getStockQuantityAttribute(): int
    {
        return $this->inventory ? max(0, $this->inventory->quantity - $this->inventory->reserved_quantity) : 0;
    }

    public function getIsInStockAttribute(): bool
    {
        if (!$this->inventory) {
            return false;
        }

        return $this->inventory->allow_backorders || $this->stock_quantity > 0;
    }

    public function getAttributesSummaryAttribute(): string
    {
        return $this->attributeValues->map(function ($val) {
            return ($val->attribute ? $val->attribute->name : 'Option') . ': ' . $val->value;
        })->implode(', ');
    }
}
