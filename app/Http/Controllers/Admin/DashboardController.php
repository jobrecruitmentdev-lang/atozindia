<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProducts = Product::count();
        $activeProducts = Product::where('status', 'active')->count();
        $inactiveProducts = Product::where('status', '!=', 'active')->count();

        $totalCategories = Category::count();
        $totalBrands = Brand::count();
        $totalCustomers = User::count();

        $totalOrders = Order::count();
        $pendingOrders = Order::where('order_status', 'pending')->count();
        $completedOrders = Order::where('order_status', 'delivered')->count();

        $totalRevenue = (float) Order::where('payment_status', 'paid')->sum('total_amount');
        if ($totalRevenue == 0) {
            // Include confirmed/processing orders if paid is pending in dev
            $totalRevenue = (float) Order::sum('total_amount');
        }

        $lowStockCount = Inventory::where('quantity', '>', 0)
            ->whereRaw('quantity <= low_stock_threshold')
            ->count();

        $outOfStockCount = Inventory::where('quantity', '<=', 0)
            ->where('allow_backorders', false)
            ->count();

        $recentOrders = Order::with(['user', 'items'])->latest()->take(5)->get();
        $recentCustomers = User::latest()->take(5)->get();

        $lowStockProducts = ProductVariant::with(['product', 'inventory'])
            ->whereHas('inventory', function ($q) {
                $q->whereRaw('quantity <= low_stock_threshold');
            })
            ->take(6)
            ->get();

        return view('admin.dashboard.index', compact(
            'totalProducts',
            'activeProducts',
            'inactiveProducts',
            'totalCategories',
            'totalBrands',
            'totalCustomers',
            'totalOrders',
            'pendingOrders',
            'completedOrders',
            'totalRevenue',
            'lowStockCount',
            'outOfStockCount',
            'recentOrders',
            'recentCustomers',
            'lowStockProducts'
        ));
    }
}
