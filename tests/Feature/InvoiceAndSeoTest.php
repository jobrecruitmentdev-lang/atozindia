<?php

namespace Tests\Feature;

use App\Mail\OrderConfirmationMail;
use App\Mail\OrderDispatchedMail;
use App\Mail\WelcomeCollectorMail;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceAndSeoTest extends TestCase
{
    public function test_sitemap_xml_generates_valid_structure_and_urls(): void
    {
        $response = $this->get('/sitemap.xml');

        $response->assertStatus(200);
        $this->assertStringContainsString('<?xml version="1.0" encoding="UTF-8"?>', $response->getContent());
        $this->assertStringContainsString('<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"', $response->getContent());
        $this->assertStringContainsString('/shop', $response->getContent());
    }

    public function test_robots_txt_returns_valid_disallow_rules(): void
    {
        $response = $this->get('/robots.txt');

        $response->assertStatus(200);
        $this->assertStringContainsString('Disallow: /admin/', $response->getContent());
        $this->assertStringContainsString('Sitemap:', $response->getContent());
    }

    public function test_order_invoice_renders_successfully(): void
    {
        $order = Order::with(['items.product.categories', 'shippingAddress'])->first();

        if ($order) {
            $response = $this->get(route('order.invoice', $order->order_number));

            $response->assertStatus(200);
            $this->assertStringContainsString('Tax Invoice', $response->getContent());
            $this->assertStringContainsString('06AABCA1234F1Z5', $response->getContent());
            $this->assertStringContainsString('ATOZ GADGETZ PRIVATE LIMITED', $response->getContent());
            $this->assertStringContainsString($order->order_number, $response->getContent());
        } else {
            $this->markTestSkipped('No orders in database to test invoice.');
        }
    }

    public function test_mailables_render_html_cleanly(): void
    {
        $order = Order::with(['items', 'shippingAddress'])->first();
        $user = User::first() ?? User::factory()->make();

        if ($order) {
            $confirmationMail = new OrderConfirmationMail($order);
            $renderedConfirmation = $confirmationMail->render();
            $this->assertStringContainsString('Acquisition Confirmed', $renderedConfirmation);
            $this->assertStringContainsString($order->order_number, $renderedConfirmation);

            $dispatchedMail = new OrderDispatchedMail($order);
            $renderedDispatched = $dispatchedMail->render();
            $this->assertStringContainsString('In Secured Transit', $renderedDispatched);
        }

        $welcomeMail = new WelcomeCollectorMail($user);
        $renderedWelcome = $welcomeMail->render();
        $this->assertStringContainsString('Collector Membership Circle', $renderedWelcome);
    }
}
