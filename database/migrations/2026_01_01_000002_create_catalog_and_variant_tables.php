<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('slug', 150)->unique();
            $table->string('logo_path', 255)->nullable();
            $table->text('description')->nullable();
            $table->string('website', 255)->nullable();
            $table->boolean('is_featured')->default(false)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->string('seo_title', 255)->nullable();
            $table->text('seo_description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('categories')->onDelete('cascade');
            $table->string('name', 150);
            $table->string('slug', 150)->unique();
            $table->text('description')->nullable();
            $table->string('image_path', 255)->nullable();
            $table->string('banner_path', 255)->nullable();
            $table->boolean('is_featured')->default(false)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->integer('sort_order')->default(0);
            $table->string('seo_title', 255)->nullable();
            $table->text('seo_description')->nullable();
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->nullable()->constrained('brands')->onDelete('set null');
            $table->string('name', 255)->index();
            $table->string('slug', 255)->unique();
            $table->string('base_sku', 64)->unique();
            $table->string('barcode', 64)->nullable()->index();
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->json('specifications_json')->nullable();
            $table->decimal('price', 12, 2);
            $table->decimal('sale_price', 12, 2)->nullable();
            $table->decimal('cost_price', 12, 2)->nullable();
            $table->decimal('tax_rate', 5, 2)->default(18.00);
            $table->decimal('weight', 8, 2)->nullable();
            $table->string('weight_unit', 10)->default('kg');
            $table->string('dimensions', 64)->nullable();
            $table->string('material', 100)->nullable();
            $table->string('manufacturer', 150)->nullable();
            $table->string('country_of_origin', 100)->nullable();
            $table->boolean('is_variable')->default(false);
            $table->enum('status', ['draft', 'active', 'archived'])->default('draft')->index();
            $table->boolean('is_featured')->default(false)->index();
            $table->boolean('is_bestseller')->default(false)->index();
            $table->boolean('is_new_arrival')->default(false)->index();
            $table->string('seo_title', 255)->nullable();
            $table->text('seo_description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('product_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['product_id', 'category_id']);
        });

        Schema::create('attributes', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('code', 100)->unique();
            $table->enum('type', ['select', 'color', 'button', 'radio'])->default('select');
            $table->timestamps();
        });

        Schema::create('attribute_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attribute_id')->constrained('attributes')->onDelete('cascade');
            $table->string('value', 150);
            $table->string('color_hex', 30)->nullable();
            $table->string('swatch_image', 255)->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['attribute_id', 'sort_order']);
        });

        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('attribute_value_id')->nullable()->constrained('attribute_values')->onDelete('set null');
            $table->string('image_path', 255);
            $table->string('alt_text', 255)->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            $table->index(['product_id', 'attribute_value_id', 'sort_order']);
        });

        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('sku', 64)->unique();
            $table->string('barcode', 64)->nullable()->index();
            $table->decimal('price_override', 12, 2)->nullable();
            $table->decimal('sale_price_override', 12, 2)->nullable();
            $table->decimal('cost_price_override', 12, 2)->nullable();
            $table->decimal('weight_override', 8, 2)->nullable();
            $table->string('dimensions_override', 64)->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active')->index();
            $table->timestamps();
        });

        Schema::create('variant_attribute_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_variant_id')->constrained('product_variants')->onDelete('cascade');
            $table->foreignId('attribute_value_id')->constrained('attribute_values')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['product_variant_id', 'attribute_value_id'], 'uk_var_attr_val');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('variant_attribute_values');
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('product_images');
        Schema::dropIfExists('attribute_values');
        Schema::dropIfExists('attributes');
        Schema::dropIfExists('product_categories');
        Schema::dropIfExists('products');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('brands');
    }
};
