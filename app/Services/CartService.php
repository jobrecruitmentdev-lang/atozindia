<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StoreSetting;
use App\Models\User;
use Illuminate\Support\Facades\Session;
use Exception;

class CartService
{
    public function getOrCreateCart(?User $user = null): Cart
    {
        if ($user) {
            $cart = Cart::firstOrCreate(['user_id' => $user->id]);
            return $cart->load(['items.product.images', 'items.variant.attributeValues.attribute']);
        }

        $sessionId = Session::getId();
        $cart = Cart::firstOrCreate(['session_id' => $sessionId, 'user_id' => null]);
        return $cart->load(['items.product.images', 'items.variant.attributeValues.attribute']);
    }

    public function addItem(int $productId, ?int $variantId = null, int $quantity = 1): CartItem
    {
        $product = Product::active()->findOrFail($productId);
        $cart = $this->getOrCreateCart(auth()->user());

        $variant = null;
        if ($variantId) {
            $variant = ProductVariant::where('product_id', $productId)->where('status', 'active')->findOrFail($variantId);
            $unitPrice = $variant->effective_price;
            $stock = $variant->stock_quantity;
        } else {
            // If product is variable, require a variant
            if ($product->is_variable) {
                $variant = $product->activeVariants()->firstOrFail();
                $variantId = $variant->id;
                $unitPrice = $variant->effective_price;
                $stock = $variant->stock_quantity;
            } else {
                // simple product variant
                $variant = $product->variants()->first();
                $variantId = $variant ? $variant->id : null;
                $unitPrice = $product->effective_price;
                $stock = $variant ? $variant->stock_quantity : 999;
            }
        }

        if ($variant && !$variant->is_in_stock && $stock < $quantity) {
            throw new Exception("Sorry, selected option is currently out of stock.");
        }

        $item = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $productId)
            ->where('product_variant_id', $variantId)
            ->first();

        if ($item) {
            $newQty = $item->quantity + $quantity;
            if ($variant && $variant->stock_quantity < $newQty && !$variant->inventory?->allow_backorders) {
                throw new Exception("Cannot add more. Maximum available stock is {$variant->stock_quantity}.");
            }
            $item->quantity = $newQty;
            $item->unit_price = $unitPrice;
            $item->save();
        } else {
            $item = CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $productId,
                'product_variant_id' => $variantId,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
            ]);
        }

        return $item;
    }

    public function updateQuantity(int $itemId, int $quantity): CartItem
    {
        $cart = $this->getOrCreateCart(auth()->user());
        $item = CartItem::where('cart_id', $cart->id)->findOrFail($itemId);

        if ($quantity <= 0) {
            $item->delete();
            return $item;
        }

        if ($item->variant && $item->variant->stock_quantity < $quantity && !$item->variant->inventory?->allow_backorders) {
            throw new Exception("Maximum available stock for this variant is {$item->variant->stock_quantity}.");
        }

        $item->quantity = $quantity;
        $item->save();

        return $item;
    }

    public function removeItem(int $itemId): void
    {
        $cart = $this->getOrCreateCart(auth()->user());
        CartItem::where('cart_id', $cart->id)->where('id', $itemId)->delete();
    }

    public function clearCart(): void
    {
        $cart = $this->getOrCreateCart(auth()->user());
        $cart->items()->delete();
        $cart->coupon_code = null;
        $cart->save();
    }

    public function applyCoupon(string $code): array
    {
        $cart = $this->getOrCreateCart(auth()->user());
        $coupon = Coupon::where('code', trim($code))->where('is_active', true)->first();

        if (!$coupon) {
            return ['success' => false, 'message' => 'Invalid or expired coupon code.'];
        }

        $subtotal = $cart->subtotal;
        if (!$coupon->isValidForSubtotal($subtotal)) {
            return ['success' => false, 'message' => "This coupon requires a minimum spend of ₹" . number_format($coupon->min_spend, 2)];
        }

        $cart->coupon_code = $coupon->code;
        $cart->save();

        $discount = $coupon->calculateDiscount($subtotal);

        return [
            'success' => true,
            'message' => "Coupon '{$coupon->code}' applied successfully!",
            'discount' => $discount,
        ];
    }

    public function removeCoupon(): void
    {
        $cart = $this->getOrCreateCart(auth()->user());
        $cart->coupon_code = null;
        $cart->save();
    }

    public function getCartTotals(?Cart $cart = null): array
    {
        $cart = $cart ?: $this->getOrCreateCart(auth()->user());
        $subtotal = $cart->subtotal;

        $discount = 0.00;
        $coupon = null;
        if ($cart->coupon_code) {
            $coupon = Coupon::where('code', $cart->coupon_code)->where('is_active', true)->first();
            if ($coupon && $coupon->isValidForSubtotal($subtotal)) {
                $discount = $coupon->calculateDiscount($subtotal);
            } else {
                $cart->coupon_code = null;
                $cart->save();
            }
        }

        $threshold = (float) StoreSetting::get('free_shipping_threshold', 2999);
        $standardShipping = (float) StoreSetting::get('standard_shipping_rate', 149);
        $shipping = ($subtotal - $discount) >= $threshold || $subtotal == 0 ? 0.00 : $standardShipping;

        $taxRate = (float) StoreSetting::get('tax_rate', 18);
        $taxIncluded = (bool) StoreSetting::get('tax_included_in_price', true);

        if ($taxIncluded) {
            // Tax is embedded in the price
            $taxAmount = round((($subtotal - $discount) * $taxRate) / (100 + $taxRate), 2);
            $total = max(0, ($subtotal - $discount) + $shipping);
        } else {
            $taxAmount = round((($subtotal - $discount) * $taxRate) / 100, 2);
            $total = max(0, ($subtotal - $discount) + $taxAmount + $shipping);
        }

        return [
            'subtotal' => $subtotal,
            'discount' => $discount,
            'coupon' => $coupon,
            'shipping' => $shipping,
            'free_shipping_threshold' => $threshold,
            'remaining_for_free_shipping' => max(0, $threshold - ($subtotal - $discount)),
            'tax_amount' => $taxAmount,
            'tax_rate' => $taxRate,
            'tax_included' => $taxIncluded,
            'total' => $total,
            'items_count' => $cart->items_count,
        ];
    }

    public function mergeGuestCart(string $sessionId, User $user): void
    {
        $guestCart = Cart::where('session_id', $sessionId)->whereNull('user_id')->first();
        if (!$guestCart) {
            return;
        }

        $userCart = Cart::firstOrCreate(['user_id' => $user->id]);

        foreach ($guestCart->items as $gItem) {
            $existing = CartItem::where('cart_id', $userCart->id)
                ->where('product_id', $gItem->product_id)
                ->where('product_variant_id', $gItem->product_variant_id)
                ->first();

            if ($existing) {
                $existing->quantity += $gItem->quantity;
                $existing->save();
            } else {
                $gItem->cart_id = $userCart->id;
                $gItem->save();
            }
        }

        if ($guestCart->coupon_code && !$userCart->coupon_code) {
            $userCart->coupon_code = $guestCart->coupon_code;
            $userCart->save();
        }

        $guestCart->delete();
    }
}
