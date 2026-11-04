<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShipmentEvent extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'shipment_id',
        'status',
        'location',
        'description',
        'event_time',
        'created_at',
    ];

    protected $casts = [
        'event_time' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }
}
