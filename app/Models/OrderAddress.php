<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'type',
        'first_name',
        'last_name',
        'company',
        'address_line_1',
        'address_line_2',
        'landmark',
        'city',
        'state',
        'postal_code',
        'country',
        'phone',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function getAddressLine1Attribute(): ?string
    {
        return $this->attributes['address_line_1'] ?? null;
    }

    public function getAddressLine2Attribute(): ?string
    {
        return $this->attributes['address_line_2'] ?? null;
    }

    public function getFormattedAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address_line_1,
            $this->address_line_2,
            $this->landmark ? "Near {$this->landmark}" : null,
            "{$this->city}, {$this->state} {$this->postal_code}",
            $this->country,
        ]);

        return implode(', ', $parts);
    }
}
