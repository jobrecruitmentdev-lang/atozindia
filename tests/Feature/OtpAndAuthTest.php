<?php

namespace Tests\Feature;

use App\Mail\OrderAuthorizationOtpMail;
use App\Mail\RegistrationOtpMail;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class OtpAndAuthTest extends TestCase
{
    /**
     * Test guest user attempting to access checkout is blocked and redirected to login.
     */
    public function test_guest_is_redirected_to_login_when_accessing_checkout(): void
    {
        $response = $this->get('/checkout');
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    /**
     * Test guest cannot send order OTP or process checkout without authentication.
     */
    public function test_guest_cannot_invoke_checkout_otp_endpoints(): void
    {
        $sendResponse = $this->postJson('/checkout/send-otp', [
            'email' => 'guest@example.com',
            'phone' => '+91 99999 11111',
            'payment_method' => 'cod',
            'shipping' => [
                'first_name' => 'Anonymous',
                'address_line_1' => 'Street 1',
                'city' => 'Delhi',
                'state' => 'Delhi',
                'postal_code' => '110001',
            ],
        ]);
        $sendResponse->assertStatus(401);

        $verifyResponse = $this->postJson('/checkout/verify-otp', ['otp' => '123456']);
        $verifyResponse->assertStatus(401);
    }

    /**
     * Test customer registration requires 6-digit OTP verification before account creation.
     */
    public function test_registration_dispatches_otp_and_requires_verification(): void
    {
        Mail::fake();

        $testEmail = 'newcollector_' . time() . '@example.com';

        // 1. Submit registration form
        $response = $this->post('/register', [
            'name' => 'Lord Harrington',
            'email' => $testEmail,
            'phone' => '+91 98765 43210',
            'password' => 'Password@123',
            'password_confirmation' => 'Password@123',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect(route('register.otp'));

        // Verify User NOT created yet in database
        $this->assertDatabaseMissing('users', ['email' => $testEmail]);

        // Verify OTP mail dispatched
        Mail::assertSent(RegistrationOtpMail::class, function ($mail) use ($testEmail) {
            return $mail->hasTo($testEmail);
        });

        // 2. Retrieve generated OTP from session
        $generatedOtp = session('registration_otp');
        $this->assertNotEmpty($generatedOtp);
        $this->assertEquals(6, strlen($generatedOtp));

        // 3. Test invalid OTP failure
        $failResponse = $this->post('/register/verify-otp', [
            'otp' => '000000',
        ]);
        $failResponse->assertSessionHasErrors('otp');
        $this->assertDatabaseMissing('users', ['email' => $testEmail]);

        // 4. Test valid OTP verification
        $successResponse = $this->post('/register/verify-otp', [
            'otp' => $generatedOtp,
        ]);

        $successResponse->assertStatus(302);
        $successResponse->assertRedirect(route('customer.dashboard'));

        // Verify User IS created, active, email_verified_at set, and authenticated
        $user = User::where('email', $testEmail)->first();
        $this->assertNotNull($user);
        $this->assertEquals('Lord Harrington', $user->name);
        $this->assertEquals('active', $user->status);
        $this->assertNotNull($user->email_verified_at);
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Test order placement requires 6-digit Order Authorization OTP.
     */
    public function test_order_placement_requires_and_verifies_order_otp(): void
    {
        Mail::fake();

        $user = User::where('email', 'customer@example.com')->first();
        $this->actingAs($user);

        $product = Product::active()->whereHas('variants.inventory', fn($q) => $q->where('quantity', '>', 0))->first();
        $variant = $product->variants->first(fn($v) => $v->inventory && $v->inventory->quantity > 0);

        // Add item to cart
        $this->postJson('/cart/add', [
            'product_id' => $product->id,
            'variant_id' => $variant ? $variant->id : null,
            'quantity' => 1,
        ]);

        // 1. Dispatch 6-digit Order OTP
        $sendResponse = $this->postJson(route('checkout.send-otp'), [
            'email' => $user->email,
            'phone' => '+91 98765 12345',
            'payment_method' => 'cod',
            'notes' => 'Concierge delivery',
            'shipping' => [
                'first_name' => 'Arthur',
                'last_name' => 'Pendleton',
                'address_line_1' => '24 King Street',
                'city' => 'Bengaluru',
                'state' => 'Karnataka',
                'postal_code' => '560001',
                'country' => 'India',
            ],
        ]);

        $sendResponse->assertStatus(200);
        $sendResponse->assertJson(['success' => true]);

        // Verify OrderAuthorizationOtpMail was dispatched
        Mail::assertSent(OrderAuthorizationOtpMail::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });

        $orderOtp = session('order_otp');
        $this->assertNotEmpty($orderOtp);
        $this->assertEquals(6, strlen($orderOtp));

        // 2. Attempt verification with incorrect OTP
        $wrongResponse = $this->postJson(route('checkout.verify-otp'), [
            'otp' => '999999',
        ]);
        $wrongResponse->assertStatus(422);
        $wrongResponse->assertJson(['success' => false]);

        // 3. Finalize order with correct OTP
        $validResponse = $this->postJson(route('checkout.verify-otp'), [
            'otp' => $orderOtp,
        ]);

        $validResponse->assertStatus(200);
        $validResponse->assertJson(['success' => true]);
        $this->assertArrayHasKey('redirect_url', $validResponse->json());

        // Verify order exists in DB
        $order = Order::where('user_id', $user->id)->latest()->first();
        $this->assertNotNull($order);
        $this->assertEquals('Bengaluru', $order->shippingAddress->city);
        $this->assertEquals('cod', $order->payment_method);
    }
}
