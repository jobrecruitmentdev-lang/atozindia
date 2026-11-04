<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'carrier_name',
        'tracking_number',
        'tracking_url',
        'shipping_cost',
        'dispatched_at',
        'estimated_delivery_at',
        'delivered_at',
        'status',
        'notes',
    ];

    protected $casts = [
        'shipping_cost' => 'decimal:2',
        'dispatched_at' => 'datetime',
        'estimated_delivery_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(ShipmentEvent::class)->orderBy('event_time', 'desc');
    }
}
