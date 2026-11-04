<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentWebhookEvent extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'provider',
        'event_id',
        'event_type',
        'payload_json',
        'is_processed',
        'processed_at',
        'created_at',
    ];

    protected $casts = [
        'payload_json' => 'array',
        'is_processed' => 'boolean',
        'processed_at' => 'datetime',
        'created_at' => 'datetime',
    ];
}
