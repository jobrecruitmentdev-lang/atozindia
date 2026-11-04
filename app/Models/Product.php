<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'brand_id',
        'name',
        'slug',
        'base_sku',
        'barcode',
        'short_description',
        'description',
        'specifications_json',
        'price',
        'sale_price',
        'cost_price',
        'tax_rate',
        'weight',
        'weight_unit',
        'dimensions',
        'material',
        'manufacturer',
        'country_of_origin',
        'is_variable',
        'status',
        'is_featured',
        'is_bestseller',
        'is_new_arrival',
        'seo_title',
        'seo_description',
    ];

    protected $casts = [
        'specifications_json' => 'array',
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'weight' => 'decimal:2',
        'is_variable' => 'boolean',
        'is_featured' => 'boolean',
        'is_bestseller' => 'boolean',
        'is_new_arrival' => 'boolean',
    ];

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'product_categories');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order', 'asc');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function activeVariants(): HasMany
    {
        return $this->hasMany(ProductVariant::class)->where('status', 'active');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class)->where('is_approved', true);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeBestseller(Builder $query): Builder
    {
        return $query->where('is_bestseller', true);
    }

    public function scopeNewArrival(Builder $query): Builder
    {
        return $query->where('is_new_arrival', true);
    }

    public function getEffectivePriceAttribute(): float
    {
        if ($this->sale_price !== null && $this->sale_price > 0) {
            return (float) $this->sale_price;
        }

        return (float) $this->price;
    }

    public function getDiscountPercentageAttribute(): ?int
    {
        if ($this->sale_price !== null && $this->sale_price > 0 && $this->price > $this->sale_price) {
            return (int) round((($this->price - $this->sale_price) / $this->price) * 100);
        }

        return null;
    }

    public function getPrimaryImageAttribute(): ?string
    {
        $primary = $this->images->firstWhere('is_primary', true);
        if ($primary) {
            return $primary->image_path;
        }

        $first = $this->images->first();
        return $first ? $first->image_path : null;
    }

    public function getSecondaryImageAttribute(): ?string
    {
        $images = $this->images;
        if ($images->count() > 1) {
            return $images->get(1)->image_path;
        }

        return $this->primary_image;
    }

    public function getColorSwatchesAttribute()
    {
        // Extract distinct colors from associated images or variants
        $colors = collect();

        // 1. From images linked to color attribute_value_id
        $this->images->each(function ($img) use (&$colors) {
            if ($img->colorValue && !$colors->contains('id', $img->colorValue->id)) {
                $colors->push($img->colorValue);
            }
        });

        // 2. From variants
        if ($colors->isEmpty()) {
            $this->variants->each(function ($variant) use (&$colors) {
                $variant->attributeValues->each(function ($attrVal) use (&$colors) {
                    if ($attrVal->color_hex && !$colors->contains('id', $attrVal->id)) {
                        $colors->push($attrVal);
                    }
                });
            });
        }

        return $colors;
    }

    public function getAverageRatingAttribute(): float
    {
        $avg = $this->reviews->avg('rating');
        return $avg ? round((float) $avg, 1) : 5.0;
    }

    public function getReviewsCountAttribute(): int
    {
        return $this->reviews->count();
    }
}
