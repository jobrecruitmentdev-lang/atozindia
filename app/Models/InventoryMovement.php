<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryMovement extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'inventory_id',
        'change_amount',
        'previous_qty',
        'new_qty',
        'type',
        'reference_type',
        'reference_id',
        'admin_id',
        'notes',
        'created_at',
    ];

    protected $casts = [
        'change_amount' => 'integer',
        'previous_qty' => 'integer',
        'new_qty' => 'integer',
        'created_at' => 'datetime',
    ];

    public function inventory(): BelongsTo
    {
        return $this->belongsTo(Inventory::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }
}
