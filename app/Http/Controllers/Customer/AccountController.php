<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Order;
use App\Models\Wishlist;
use App\Models\WishlistItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user()->load(['addresses', 'orders' => function ($q) {
            $q->latest()->take(5);
        }]);

        return view('customer.dashboard', compact('user'));
    }

    public function orders()
    {
        $orders = auth()->user()->orders()->with(['items', 'shippingAddress'])->latest()->paginate(10);
        return view('customer.orders', compact('orders'));
    }

    public function orderDetail(string $orderNumber)
    {
        $order = auth()->user()->orders()
            ->with(['items.variant', 'shippingAddress', 'billingAddress', 'statusHistories'])
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        return view('customer.order-detail', compact('order'));
    }

    public function addresses()
    {
        $addresses = auth()->user()->addresses;
        return view('customer.addresses', compact('addresses'));
    }

    public function storeAddress(Request $request)
    {
        $data = $request->validate([
            'type' => 'required|in:shipping,billing,both',
            'first_name' => 'required|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'company' => 'nullable|string|max:150',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'landmark' => 'nullable|string|max:150',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'country' => 'nullable|string|max:10',
            'phone' => 'required|string|max:32',
            'is_default_shipping' => 'nullable|boolean',
            'is_default_billing' => 'nullable|boolean',
        ]);

        $data['user_id'] = auth()->id();
        $data['is_default_shipping'] = (bool) $request->boolean('is_default_shipping');
        $data['is_default_billing'] = (bool) $request->boolean('is_default_billing');

        if ($data['is_default_shipping']) {
            Address::where('user_id', auth()->id())->update(['is_default_shipping' => false]);
        }
        if ($data['is_default_billing']) {
            Address::where('user_id', auth()->id())->update(['is_default_billing' => false]);
        }

        Address::create($data);

        return back()->with('success', 'Address added to your address book.');
    }

    public function deleteAddress(Address $address)
    {
        if ($address->user_id !== auth()->id()) {
            abort(403);
        }

        $address->delete();
        return back()->with('success', 'Address removed.');
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'name' => 'required|string|max:191',
            'email' => "required|email|max:191|unique:users,email,{$user->id}",
            'phone' => 'nullable|string|max:32',
            'current_password' => 'nullable|string',
            'new_password' => 'nullable|string|min:8|confirmed',
        ]);

        if (!empty($data['new_password'])) {
            if (empty($data['current_password']) || !Hash::check($data['current_password'], $user->password)) {
                return back()->withErrors(['current_password' => 'Current password does not match.']);
            }
            $user->password = Hash::make($data['new_password']);
        }

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->phone = $data['phone'] ?? null;
        $user->save();

        return back()->with('success', 'Your personal details have been updated.');
    }

    public function wishlist()
    {
        $wishlist = Wishlist::firstOrCreate(['user_id' => auth()->id()]);
        $wishlist->load('items.product.images');

        return view('customer.wishlist', compact('wishlist'));
    }

    public function toggleWishlist(Request $request)
    {
        $request->validate(['product_id' => 'required|exists:products,id']);

        $wishlist = Wishlist::firstOrCreate(['user_id' => auth()->id()]);
        $item = WishlistItem::where('wishlist_id', $wishlist->id)
            ->where('product_id', $request->product_id)
            ->first();

        if ($item) {
            $item->delete();
            $added = false;
            $message = 'Removed from your wishlist.';
        } else {
            WishlistItem::create([
                'wishlist_id' => $wishlist->id,
                'product_id' => $request->product_id,
            ]);
            $added = true;
            $message = 'Saved to your wishlist.';
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'added' => $added,
                'count' => $wishlist->items()->count(),
                'message' => $message,
            ]);
        }

        return back()->with('success', $message);
    }
}
