<?php

namespace App\Services;

use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Exception;

class InventoryService
{
    /**
     * Adjust stock with row locking to prevent race conditions.
     */
    public function adjustStock(ProductVariant $variant, int $newQuantity, string $type = 'manual_adjust', ?string $notes = null, ?int $adminId = null): Inventory
    {
        return DB::transaction(function () use ($variant, $newQuantity, $type, $notes, $adminId) {
            $inventory = Inventory::where('product_variant_id', $variant->id)->lockForUpdate()->first();

            if (!$inventory) {
                $inventory = Inventory::create([
                    'product_variant_id' => $variant->id,
                    'quantity' => 0,
                    'reserved_quantity' => 0,
                    'low_stock_threshold' => 5,
                ]);
            }

            $previousQty = $inventory->quantity;
            $diff = $newQuantity - $previousQty;

            $inventory->quantity = max(0, $newQuantity);
            $inventory->save();

            InventoryMovement::create([
                'inventory_id' => $inventory->id,
                'change_amount' => $diff,
                'previous_qty' => $previousQty,
                'new_qty' => $inventory->quantity,
                'type' => $type,
                'admin_id' => $adminId,
                'notes' => $notes,
                'created_at' => now(),
            ]);

            return $inventory;
        });
    }

    /**
     * Deduct stock when an order is finalized.
     */
    public function deductStockForOrder(ProductVariant $variant, int $quantity, int $orderId): void
    {
        DB::transaction(function () use ($variant, $quantity, $orderId) {
            $inventory = Inventory::where('product_variant_id', $variant->id)->lockForUpdate()->firstOrFail();

            if ($inventory->quantity < $quantity && !$inventory->allow_backorders) {
                throw new Exception("Insufficient stock for SKU {$variant->sku}. Available: {$inventory->quantity}, Requested: {$quantity}");
            }

            $previousQty = $inventory->quantity;
            $inventory->quantity = max(0, $inventory->quantity - $quantity);
            $inventory->save();

            InventoryMovement::create([
                'inventory_id' => $inventory->id,
                'change_amount' => -$quantity,
                'previous_qty' => $previousQty,
                'new_qty' => $inventory->quantity,
                'type' => 'order_deduct',
                'reference_type' => 'Order',
                'reference_id' => $orderId,
                'notes' => "Deducted for Order #{$orderId}",
                'created_at' => now(),
            ]);
        });
    }

    /**
     * Restore stock when an order is cancelled or returned.
     */
    public function restoreStockForOrder(ProductVariant $variant, int $quantity, int $orderId, string $type = 'order_cancel'): void
    {
        DB::transaction(function () use ($variant, $quantity, $orderId, $type) {
            $inventory = Inventory::where('product_variant_id', $variant->id)->lockForUpdate()->firstOrFail();

            $previousQty = $inventory->quantity;
            $inventory->quantity += $quantity;
            $inventory->save();

            InventoryMovement::create([
                'inventory_id' => $inventory->id,
                'change_amount' => $quantity,
                'previous_qty' => $previousQty,
                'new_qty' => $inventory->quantity,
                'type' => $type,
                'reference_type' => 'Order',
                'reference_id' => $orderId,
                'notes' => "Restored from {$type} for Order #{$orderId}",
                'created_at' => now(),
            ]);
        });
    }
}
