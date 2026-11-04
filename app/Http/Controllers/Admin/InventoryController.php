<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Services\InventoryService;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    protected InventoryService $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    public function index(Request $request)
    {
        $query = Inventory::with(['variant.product.brand', 'variant.attributeValues.attribute']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->whereHas('variant', function ($q) use ($s) {
                $q->where('sku', 'like', "%{$s}%")
                    ->orWhereHas('product', function ($pq) use ($s) {
                        $pq->where('name', 'like', "%{$s}%");
                    });
            });
        }

        if ($request->filled('filter')) {
            if ($request->filter === 'low_stock') {
                $query->where('quantity', '>', 0)->whereRaw('quantity <= low_stock_threshold');
            } elseif ($request->filter === 'out_of_stock') {
                $query->where('quantity', '<=', 0);
            }
        }

        $inventories = $query->paginate(20)->withQueryString();
        $movements = InventoryMovement::with(['inventory.variant.product', 'admin'])
            ->latest('id')
            ->take(15)
            ->get();

        return view('admin.inventory.index', compact('inventories', 'movements'));
    }

    public function update(Request $request, Inventory $inventory)
    {
        $request->validate([
            'quantity' => 'required|integer|min:0',
            'low_stock_threshold' => 'required|integer|min:0',
            'notes' => 'nullable|string|max:255',
        ]);

        $this->inventoryService->adjustStock(
            $inventory->variant,
            $request->quantity,
            'manual_adjust',
            $request->notes ?: 'Manual adjustment from inventory screen',
            auth('admin')->id()
        );

        $inventory->low_stock_threshold = $request->low_stock_threshold;
        $inventory->save();

        return back()->with('success', "Stock for SKU {$inventory->variant->sku} updated to {$request->quantity}.");
    }
}
