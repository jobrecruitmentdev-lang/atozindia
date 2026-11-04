<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use Illuminate\Http\Request;
use Exception;

class CartController extends Controller
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function index()
    {
        $cart = $this->cartService->getOrCreateCart(auth()->user());
        $totals = $this->cartService->getCartTotals($cart);

        return view('shop.cart', compact('cart', 'totals'));
    }

    public function drawerData()
    {
        $cart = $this->cartService->getOrCreateCart(auth()->user());
        $totals = $this->cartService->getCartTotals($cart);

        $items = $cart->items->map(function ($item) {
            $variantColor = null;
            if ($item->variant) {
                $colorAttr = $item->variant->attributeValues->firstWhere('attribute.type', 'color');
                if ($colorAttr) {
                    $variantColor = $colorAttr->value;
                }
            }

            // Thumbnail
            $thumbnail = $item->product->primary_image;
            if ($item->variant && $item->variant->attributeValues->isNotEmpty()) {
                $colorVal = $item->variant->attributeValues->firstWhere('attribute.type', 'color');
                if ($colorVal) {
                    $colImg = $item->product->images->firstWhere('attribute_value_id', $colorVal->id);
                    if ($colImg) {
                        $thumbnail = $colImg->image_path;
                    }
                }
            }

            return [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'name' => $item->product->name,
                'slug' => $item->product->slug,
                'sku' => $item->variant ? $item->variant->sku : $item->product->base_sku,
                'variant_summary' => $item->variant ? $item->variant->attributes_summary : null,
                'thumbnail' => $thumbnail,
                'quantity' => $item->quantity,
                'unit_price' => '₹' . number_format($item->unit_price, 2),
                'total_price' => '₹' . number_format($item->total_price, 2),
                'stock' => $item->variant ? $item->variant->stock_quantity : 999,
            ];
        });

        return response()->json([
            'items' => $items,
            'subtotal' => '₹' . number_format($totals['subtotal'], 2),
            'discount' => '₹' . number_format($totals['discount'], 2),
            'shipping' => $totals['shipping'] > 0 ? '₹' . number_format($totals['shipping'], 2) : 'FREE',
            'tax' => '₹' . number_format($totals['tax_amount'], 2),
            'total' => '₹' . number_format($totals['total'], 2),
            'raw_total' => $totals['total'],
            'raw_subtotal' => $totals['subtotal'],
            'items_count' => $totals['items_count'],
            'coupon_code' => $cart->coupon_code,
            'remaining_for_free_shipping' => $totals['remaining_for_free_shipping'],
            'free_shipping_threshold' => $totals['free_shipping_threshold'],
        ]);
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'nullable|exists:product_variants,id',
            'quantity' => 'nullable|integer|min:1',
        ]);

        try {
            $item = $this->cartService->addItem(
                $request->product_id,
                $request->variant_id,
                $request->input('quantity', 1)
            );

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Added to your shopping bag.',
                    'cart' => $this->drawerData()->original,
                ]);
            }

            return back()->with('success', 'Added to your shopping bag.');
        } catch (Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function update(Request $request, int $itemId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:0',
        ]);

        try {
            $this->cartService->updateQuantity($itemId, $request->quantity);

            return response()->json([
                'success' => true,
                'cart' => $this->drawerData()->original,
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function remove(int $itemId)
    {
        $this->cartService->removeItem($itemId);

        return response()->json([
            'success' => true,
            'cart' => $this->drawerData()->original,
        ]);
    }

    public function applyCoupon(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $res = $this->cartService->applyCoupon($request->code);
        $res['cart'] = $this->drawerData()->original;

        return response()->json($res);
    }

    public function removeCoupon()
    {
        $this->cartService->removeCoupon();

        return response()->json([
            'success' => true,
            'message' => 'Coupon removed.',
            'cart' => $this->drawerData()->original,
        ]);
    }
}
