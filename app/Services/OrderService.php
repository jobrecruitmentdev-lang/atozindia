<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CouponRedemption;
use App\Models\Order;
use App\Models\OrderAddress;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Exception;

class OrderService
{
    protected InventoryService $inventoryService;
    protected CartService $cartService;

    public function __construct(InventoryService $inventoryService, CartService $cartService)
    {
        $this->inventoryService = $inventoryService;
        $this->cartService = $cartService;
    }

    /**
     * Create order from checkout payload.
     */
    public function createOrder(array $data, ?User $user = null): Order
    {
        return DB::transaction(function () use ($data, $user) {
            $cart = $this->cartService->getOrCreateCart($user);

            if ($cart->items->isEmpty()) {
                throw new Exception("Your shopping cart is empty.");
            }

            // 1. Validate real-time stock for every item before creating order
            foreach ($cart->items as $item) {
                if ($item->variant) {
                    $stock = $item->variant->stock_quantity;
                    if (!$item->variant->is_in_stock && $stock < $item->quantity) {
                        throw new Exception("Product '{$item->product->name}' ({$item->variant->sku}) is out of stock.");
                    }
                }
            }

            // 2. Calculate final financial figures
            $totals = $this->cartService->getCartTotals($cart);

            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'user_id' => $user?->id,
                'customer_email' => $data['email'],
                'customer_phone' => $data['phone'],
                'subtotal' => $totals['subtotal'],
                'discount_amount' => $totals['discount'],
                'tax_amount' => $totals['tax_amount'],
                'shipping_amount' => $totals['shipping'],
                'total_amount' => $totals['total'],
                'order_status' => 'pending',
                'payment_status' => 'pending',
                'payment_method' => $data['payment_method'] ?? 'cod',
                'fulfillment_status' => 'unfulfilled',
                'notes' => $data['notes'] ?? null,
                'ip_address' => request()->ip(),
            ]);

            // 3. Save Immutable Order Items & Deduct Inventory
            foreach ($cart->items as $item) {
                $variantSummary = $item->variant ? $item->variant->attributes_summary : null;
                $thumbnail = $item->product->primary_image;

                if ($item->variant && $item->variant->attributeValues->isNotEmpty()) {
                    // Try to match variant color image if available
                    $colorVal = $item->variant->attributeValues->firstWhere('attribute.type', 'color');
                    if ($colorVal) {
                        $colorImg = $item->product->images->firstWhere('attribute_value_id', $colorVal->id);
                        if ($colorImg) {
                            $thumbnail = $colorImg->image_path;
                        }
                    }
                }

                $itemSubtotal = $item->quantity * $item->unit_price;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'product_variant_id' => $item->product_variant_id,
                    'product_name' => $item->product->name,
                    'variant_sku' => $item->variant?->sku ?? $item->product->base_sku,
                    'variant_attributes_summary' => $variantSummary,
                    'thumbnail_path' => $thumbnail,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'subtotal' => $itemSubtotal,
                    'tax_amount' => 0.00,
                    'total' => $itemSubtotal,
                ]);

                // Deduct stock safely with row lock
                if ($item->variant) {
                    $this->inventoryService->deductStockForOrder($item->variant, $item->quantity, $order->id);
                }
            }

            // 4. Save Shipping Address Snapshot
            OrderAddress::create([
                'order_id' => $order->id,
                'type' => 'shipping',
                'first_name' => $data['shipping']['first_name'],
                'last_name' => $data['shipping']['last_name'] ?? null,
                'company' => $data['shipping']['company'] ?? null,
                'address_line_1' => $data['shipping']['address_line_1'],
                'address_line_2' => $data['shipping']['address_line_2'] ?? null,
                'landmark' => $data['shipping']['landmark'] ?? null,
                'city' => $data['shipping']['city'],
                'state' => $data['shipping']['state'],
                'postal_code' => $data['shipping']['postal_code'],
                'country' => $data['shipping']['country'] ?? 'IN',
                'phone' => $data['shipping']['phone'] ?? $data['phone'],
            ]);

            // 5. Save Billing Address Snapshot
            $billingData = !empty($data['same_as_shipping']) ? $data['shipping'] : ($data['billing'] ?? $data['shipping']);
            OrderAddress::create([
                'order_id' => $order->id,
                'type' => 'billing',
                'first_name' => $billingData['first_name'],
                'last_name' => $billingData['last_name'] ?? null,
                'company' => $billingData['company'] ?? null,
                'address_line_1' => $billingData['address_line_1'],
                'address_line_2' => $billingData['address_line_2'] ?? null,
                'landmark' => $billingData['landmark'] ?? null,
                'city' => $billingData['city'],
                'state' => $billingData['state'],
                'postal_code' => $billingData['postal_code'],
                'country' => $billingData['country'] ?? 'IN',
                'phone' => $billingData['phone'] ?? $data['phone'],
            ]);

            // 6. Record Coupon Redemption
            if (!empty($totals['coupon'])) {
                CouponRedemption::create([
                    'coupon_id' => $totals['coupon']->id,
                    'order_id' => $order->id,
                    'user_id' => $user?->id,
                    'discount_applied' => $totals['discount'],
                    'created_at' => now(),
                ]);

                $totals['coupon']->increment('times_used');
            }

            // 7. Initial Status History
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'status_type' => 'order_status',
                'from_status' => null,
                'to_status' => 'pending',
                'comment' => 'Order initiated successfully via ' . strtoupper($order->payment_method),
                'created_at' => now(),
            ]);

            // 8. Clear Cart
            $cart->items()->delete();
            $cart->coupon_code = null;
            $cart->save();

            return $order;
        });
    }

    /**
     * Admin update of order status.
     */
    public function updateOrderStatus(Order $order, string $newStatus, ?string $comment = null, ?int $adminId = null): void
    {
        if ($order->order_status === $newStatus) {
            return;
        }

        $oldStatus = $order->order_status;
        $order->order_status = $newStatus;
        $order->save();

        OrderStatusHistory::create([
            'order_id' => $order->id,
            'status_type' => 'order_status',
            'from_status' => $oldStatus,
            'to_status' => $newStatus,
            'comment' => $comment ?? "Status updated from {$oldStatus} to {$newStatus}",
            'admin_id' => $adminId,
            'created_at' => now(),
        ]);

        // If order cancelled, restore stock
        if ($newStatus === 'cancelled') {
            foreach ($order->items as $item) {
                if ($item->variant) {
                    $this->inventoryService->restoreStockForOrder($item->variant, $item->quantity, $order->id, 'order_cancel');
                }
            }
        }
    }
}
