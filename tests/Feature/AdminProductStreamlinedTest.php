<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminProductStreamlinedTest extends TestCase
{
    public function test_product_create_page_renders_clean_streamlined_interface(): void
    {
        $admin = Admin::first() ?? Admin::factory()->create();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.products.create'));
        $response->assertStatus(200);
        $response->assertSee('Product Title *');
        $response->assertSee('Regular Price');
        $response->assertSee('Primary Showcase Photo');
        $response->assertSee('Save & Publish Product', false);
    }

    public function test_admin_can_create_simple_product_in_one_step_without_hidden_tab_blockers(): void
    {
        $admin = Admin::first() ?? Admin::factory()->create();
        $category = Category::where('is_active', true)->first();

        $postData = [
            'name' => 'Organic Bamboo Cotton Socks (Pack of 3)',
            'price' => '499.00',
            'sale_price' => '399.00',
            'stock_quantity' => 25,
            'status' => 'active',
            'categories' => [$category->id],
            'is_variable' => 0,
            'is_bestseller' => 1,
        ];

        $response = $this->actingAs($admin, 'admin')->post(route('admin.products.store'), $postData);
        $response->assertRedirect();

        $product = Product::where('name', 'Organic Bamboo Cotton Socks (Pack of 3)')->first();
        $this->assertNotNull($product);
        $this->assertEquals(499.00, $product->price);
        $this->assertFalse((bool) $product->is_variable);
        $this->assertTrue((bool) $product->is_bestseller);

        // Check that a default variant with inventory 25 was created
        $defaultVariant = $product->variants()->first();
        $this->assertNotNull($defaultVariant);
        $this->assertEquals(25, $defaultVariant->inventory->quantity);
    }

    public function test_admin_can_add_variant_with_only_size_attribute_without_storage_or_colour(): void
    {
        $admin = Admin::first() ?? Admin::factory()->create();
        $category = Category::where('is_active', true)->first();

        // Create product
        $product = Product::create([
            'name' => 'Athletic Performance Crew Socks',
            'slug' => 'athletic-performance-crew-socks-' . uniqid(),
            'base_sku' => 'SCK-' . strtoupper(uniqid()),
            'price' => 349.00,
            'status' => 'active',
            'is_variable' => 0,
        ]);
        $product->categories()->sync([$category->id]);

        // Find a size attribute value (e.g. Free Size or L)
        $sizeAttr = Attribute::where('code', 'size')->first();
        $sizeVal = AttributeValue::where('attribute_id', $sizeAttr->id)->first();

        // Pass attribute_values with empty options for other attributes (like storage and color)
        $variantData = [
            'sku' => '', // Auto-generate
            'stock_quantity' => 50,
            'price_override' => '299.00',
            'attribute_values' => ['', '', (string) $sizeVal->id], // Only size selected!
        ];

        $response = $this->actingAs($admin, 'admin')->post(
            route('admin.products.variant.store', $product->id),
            $variantData
        );

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $freshProduct = $product->fresh();
        $this->assertTrue((bool) $freshProduct->is_variable);

        // Check variant created with the size attribute value
        $variant = $freshProduct->variants()->where('price_override', 299.00)->first();
        $this->assertNotNull($variant);
        $this->assertTrue($variant->attributeValues->contains($sizeVal->id));
        $this->assertEquals(50, $variant->inventory->quantity);
    }

    public function test_admin_can_update_simple_product_stock_and_price_directly(): void
    {
        $admin = Admin::first() ?? Admin::factory()->create();
        $category = Category::where('is_active', true)->first();

        $product = Product::where('is_variable', false)->first();

        $updateData = [
            'name' => $product->name,
            'base_sku' => $product->base_sku,
            'price' => 888.00,
            'stock_quantity' => 77,
            'status' => 'active',
            'categories' => [$category->id],
            'is_variable' => 0,
        ];

        $response = $this->actingAs($admin, 'admin')->put(
            route('admin.products.update', $product->id),
            $updateData
        );

        $response->assertRedirect();

        $fresh = $product->fresh();
        $this->assertEquals(888.00, $fresh->price);
        $this->assertEquals(77, $fresh->variants()->first()->inventory->quantity);
    }
}
