<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index(Request $request)
    {
        $query = Order::with(['user', 'items']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('order_number', 'like', "%{$s}%")
                    ->orWhere('customer_email', 'like', "%{$s}%")
                    ->orWhere('customer_phone', 'like', "%{$s}%");
            });
        }

        if ($request->filled('order_status')) {
            $query->where('order_status', $request->order_status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        $orders = $query->latest()->paginate(15)->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load([
            'user',
            'items.variant',
            'shippingAddress',
            'billingAddress',
            'statusHistories.admin',
            'payments.transactions',
            'shipments.events',
        ]);

        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status_type' => 'required|in:order_status,payment_status,fulfillment_status',
            'new_status' => 'required|string',
            'comment' => 'nullable|string|max:500',
        ]);

        $type = $request->status_type;
        $newStatus = $request->new_status;
        $oldStatus = $order->{$type};

        if ($oldStatus === $newStatus) {
            return back()->with('info', 'No status change detected.');
        }

        if ($type === 'order_status') {
            $this->orderService->updateOrderStatus($order, $newStatus, $request->comment, auth('admin')->id());
        } else {
            $order->{$type} = $newStatus;
            $order->save();

            OrderStatusHistory::create([
                'order_id' => $order->id,
                'status_type' => $type,
                'from_status' => $oldStatus,
                'to_status' => $newStatus,
                'comment' => $request->comment ?: "Updated {$type} from {$oldStatus} to {$newStatus}",
                'admin_id' => auth('admin')->id(),
                'created_at' => now(),
            ]);
        }

        AuditLog::log('status_updated', 'Order', $order->id, [$type => $oldStatus], [$type => $newStatus]);

        return back()->with('success', "Order status successfully updated to '{$newStatus}'.");
    }
}
