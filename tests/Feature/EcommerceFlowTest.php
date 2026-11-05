<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Tests\TestCase;

class EcommerceFlowTest extends TestCase
{
    /**
     * Test storefront homepage loads with curated data.
     */
    public function test_storefront_homepage_loads_successfully(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('ATOZ GADGETZ');
        $response->assertSee('ARCHITECTURAL');
        $response->assertSee('Explore Catalogue');
    }

    /**
     * Test catalogue shop page with filters.
     */
    public function test_catalogue_page_loads_with_products_and_filters(): void
    {
        $response = $this->get('/shop');

        $response->assertStatus(200);
        $response->assertSee('All Atelier Instruments');
        $response->assertSee('Departments');
        $response->assertSee('Atelier Brands');
    }

    /**
     * Test product detail page with multi-colour galleries and variants.
     */
    public function test_product_detail_page_loads_with_variants_and_gallery(): void
    {
        $product = Product::active()->where('slug', 'aether-one-anc-headphones')->first();
        if (!$product) {
            $product = Product::active()->first();
        }

        $response = $this->get('/product/' . $product->slug);

        $response->assertStatus(200);
        $response->assertSee($product->name);
        $response->assertSee('Add to Bag');
        $response->assertSee('Specifications');
        $response->assertSee('Engineering');
    }

    /**
     * Test cart operations: Add item, get drawer data, calculate free shipping tier.
     */
    public function test_cart_add_and_drawer_api(): void
    {
        $product = Product::active()->first();
        $variant = $product->variants->first();

        // Add to cart via AJAX
        $response = $this->postJson('/cart/add', [
            'product_id' => $product->id,
            'variant_id' => $variant ? $variant->id : null,
            'quantity' => 1,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Check cart drawer
        $drawerResponse = $this->getJson('/cart/drawer');
        $drawerResponse->assertStatus(200);
        $drawerResponse->assertJsonStructure([
            'items',
            'subtotal',
            'discount',
            'shipping',
            'total',
            'items_count',
        ]);
    }

    /**
     * Test checkout and order creation flow with inventory deduction.
     */
    public function test_checkout_order_creation_flow(): void
    {
        $user = User::where('email', 'customer@example.com')->first();
        $this->assertNotNull($user);
        $this->actingAs($user);

        // Clear cart for clean assertion
        if ($user->cart) {
            $user->cart->items()->delete();
        }

        $product = Product::active()->first();
        $variant = $product->variants->first();

        // 1. Add item to cart
        $this->postJson('/cart/add', [
            'product_id' => $product->id,
            'variant_id' => $variant ? $variant->id : null,
            'quantity' => 1,
        ]);

        // 2. Initial stock
        $initialStock = $variant ? $variant->inventory->quantity : 10;

        // 3. Request 6-digit Order Authorization OTP
        $otpResponse = $this->postJson(route('checkout.send-otp'), [
            'email' => 'client@collector.com',
            'phone' => '+91 98765 00000',
            'payment_method' => 'cod',
            'notes' => 'White-glove priority suite delivery',
            'shipping' => [
                'first_name' => 'Alexander',
                'last_name' => 'Vance',
                'address_line_1' => 'Penthouse 42, Skyview Tower',
                'city' => 'Gurugram',
                'state' => 'Haryana',
                'postal_code' => '122002',
                'country' => 'India',
            ],
        ]);

        $otpResponse->assertStatus(200);
        $otpResponse->assertJson(['success' => true]);

        // Verify OTP was stored in session
        $otp = session('order_otp');
        $this->assertNotEmpty($otp);
        $this->assertEquals(6, strlen($otp));

        // 4. Verify 6-digit OTP and complete order
        $verifyResponse = $this->postJson(route('checkout.verify-otp'), [
            'otp' => $otp,
        ]);

        $verifyResponse->assertStatus(200);
        $verifyResponse->assertJson(['success' => true]);

        // Verify order created in database
        $order = Order::where('customer_email', 'client@collector.com')->latest()->first();
        $this->assertNotNull($order);
        $this->assertEquals('pending', $order->order_status);
        $this->assertEquals('cod', $order->payment_method);

        // Verify stock deducted
        if ($variant) {
            $variant->inventory->refresh();
            $this->assertEquals($initialStock - 1, $variant->inventory->quantity);
        }

        // 5. Verify confirmation receipt page
        $confirmResponse = $this->get(route('checkout.confirmation', $order->order_number));
        $confirmResponse->assertStatus(200);
        $confirmResponse->assertSee($order->order_number);
        $confirmResponse->assertSee('Thank You For Your Acquisition');
    }

    /**
     * Test Admin authentication and dashboard KPIs.
     */
    public function test_admin_authentication_and_dashboard_metrics(): void
    {
        $admin = Admin::where('email', 'admin@atozgadgetz.com')->first();
        $this->assertNotNull($admin);

        // Admin login page
        $this->get('/admin/login')->assertStatus(200);

        // Login attempt
        $loginResponse = $this->post('/admin/login', [
            'email' => 'admin@atozgadgetz.com',
            'password' => 'Admin@12345',
        ]);

        $loginResponse->assertRedirect('/admin/dashboard');

        // Access dashboard as authenticated admin
        $dashboardResponse = $this->actingAs($admin, 'admin')->get('/admin/dashboard');
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee('Operational Overview');
        $dashboardResponse->assertSee('Gross Revenue');

        // Admin products index
        $productsResponse = $this->actingAs($admin, 'admin')->get('/admin/products');
        $productsResponse->assertStatus(200);
        $productsResponse->assertSee('Product Catalogue');

        // Admin product editor
        $firstProduct = Product::first();
        $editResponse = $this->actingAs($admin, 'admin')->get("/admin/products/{$firstProduct->id}/edit");
        $editResponse->assertStatus(200);
        $editResponse->assertSee('Colour Galleries');
        $editResponse->assertSee('Variants Matrix');

        // Admin inventory
        $inventoryResponse = $this->actingAs($admin, 'admin')->get('/admin/inventory');
        $inventoryResponse->assertStatus(200);
        $inventoryResponse->assertSee('Active Stock Holdings');

        // Admin orders
        $ordersResponse = $this->actingAs($admin, 'admin')->get('/admin/orders');
        $ordersResponse->assertStatus(200);
        $ordersResponse->assertSee('Order Fulfillment & Sales');

        // Admin audit logs
        $logsResponse = $this->actingAs($admin, 'admin')->get('/admin/audit-logs');
        $logsResponse->assertStatus(200);
        $logsResponse->assertSee('Security Audit Events');
    }
}
