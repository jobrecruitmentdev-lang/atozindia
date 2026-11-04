<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\AuditLog;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['brand', 'categories', 'images', 'variants.inventory']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                    ->orWhere('base_sku', 'like', "%{$s}%")
                    ->orWhere('barcode', 'like', "%{$s}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        if ($request->filled('category_id')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('categories.id', $request->category_id);
            });
        }

        $products = $query->latest()->paginate(15)->withQueryString();
        $brands = Brand::where('is_active', true)->orderBy('name')->get();
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('admin.products.index', compact('products', 'brands', 'categories'));
    }

    public function create()
    {
        $brands = Brand::where('is_active', true)->orderBy('name')->get();
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $attributes = Attribute::with('values')->get();

        return view('admin.products.create', compact('brands', 'categories', 'attributes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'base_sku' => 'required|string|max:64|unique:products,base_sku',
            'brand_id' => 'nullable|exists:brands,id',
            'categories' => 'required|array',
            'categories.*' => 'exists:categories,id',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'short_description' => 'nullable|string',
            'description' => 'nullable|string',
            'weight' => 'nullable|numeric',
            'weight_unit' => 'nullable|string|max:10',
            'dimensions' => 'nullable|string|max:64',
            'material' => 'nullable|string|max:100',
            'manufacturer' => 'nullable|string|max:150',
            'country_of_origin' => 'nullable|string|max:100',
            'status' => 'required|in:draft,active,archived',
            'is_featured' => 'nullable|boolean',
            'is_bestseller' => 'nullable|boolean',
            'is_new_arrival' => 'nullable|boolean',
            'is_variable' => 'nullable|boolean',
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string',
            'stock_quantity' => 'nullable|integer|min:0',
        ]);

        $slug = Str::slug($data['name']);
        $originalSlug = $slug;
        $count = 1;
        while (Product::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-" . $count++;
        }
        $data['slug'] = $slug;
        $data['is_featured'] = (bool) $request->boolean('is_featured');
        $data['is_bestseller'] = (bool) $request->boolean('is_bestseller');
        $data['is_new_arrival'] = (bool) $request->boolean('is_new_arrival');
        $data['is_variable'] = (bool) $request->boolean('is_variable');

        // Handle specifications array
        if ($request->has('spec_keys') && is_array($request->spec_keys)) {
            $specs = [];
            foreach ($request->spec_keys as $idx => $k) {
                if (!empty($k) && isset($request->spec_values[$idx])) {
                    $specs[$k] = $request->spec_values[$idx];
                }
            }
            $data['specifications_json'] = $specs;
        }

        $product = DB::transaction(function () use ($data, $request) {
            $categories = $data['categories'];
            unset($data['categories']);
            $stockQty = $data['stock_quantity'] ?? 0;
            unset($data['stock_quantity']);

            $product = Product::create($data);
            $product->categories()->sync($categories);

            // If not variable, create a single default variant
            if (!$product->is_variable) {
                $variant = ProductVariant::create([
                    'product_id' => $product->id,
                    'sku' => $product->base_sku,
                    'status' => 'active',
                ]);

                $inv = Inventory::create([
                    'product_variant_id' => $variant->id,
                    'quantity' => $stockQty,
                    'reserved_quantity' => 0,
                    'low_stock_threshold' => 5,
                ]);

                if ($stockQty > 0) {
                    InventoryMovement::create([
                        'inventory_id' => $inv->id,
                        'change_amount' => $stockQty,
                        'previous_qty' => 0,
                        'new_qty' => $stockQty,
                        'type' => 'purchase',
                        'admin_id' => auth('admin')->id(),
                        'notes' => 'Initial stock on creation',
                    ]);
                }
            }

            AuditLog::log('created', 'Product', $product->id, null, $product->toArray());

            return $product;
        });

        return redirect()->route('admin.products.edit', $product->id)
            ->with('success', 'Product created successfully! You can now manage multi-colour image galleries and variants below.');
    }

    public function edit(Product $product)
    {
        $product->load([
            'brand',
            'categories',
            'images.colorValue',
            'variants.attributeValues.attribute',
            'variants.inventory',
        ]);

        $brands = Brand::where('is_active', true)->orderBy('name')->get();
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $attributes = Attribute::with('values')->get();

        return view('admin.products.edit', compact('product', 'brands', 'categories', 'attributes'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'base_sku' => "required|string|max:64|unique:products,base_sku,{$product->id}",
            'brand_id' => 'nullable|exists:brands,id',
            'categories' => 'required|array',
            'categories.*' => 'exists:categories,id',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'short_description' => 'nullable|string',
            'description' => 'nullable|string',
            'weight' => 'nullable|numeric',
            'weight_unit' => 'nullable|string|max:10',
            'dimensions' => 'nullable|string|max:64',
            'material' => 'nullable|string|max:100',
            'manufacturer' => 'nullable|string|max:150',
            'country_of_origin' => 'nullable|string|max:100',
            'status' => 'required|in:draft,active,archived',
            'is_featured' => 'nullable|boolean',
            'is_bestseller' => 'nullable|boolean',
            'is_new_arrival' => 'nullable|boolean',
            'is_variable' => 'nullable|boolean',
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string',
        ]);

        $data['is_featured'] = (bool) $request->boolean('is_featured');
        $data['is_bestseller'] = (bool) $request->boolean('is_bestseller');
        $data['is_new_arrival'] = (bool) $request->boolean('is_new_arrival');
        $data['is_variable'] = (bool) $request->boolean('is_variable');

        // Handle specifications array
        if ($request->has('spec_keys') && is_array($request->spec_keys)) {
            $specs = [];
            foreach ($request->spec_keys as $idx => $k) {
                if (!empty($k) && isset($request->spec_values[$idx])) {
                    $specs[$k] = $request->spec_values[$idx];
                }
            }
            $data['specifications_json'] = $specs;
        }

        DB::transaction(function () use ($data, $product) {
            $categories = $data['categories'];
            unset($data['categories']);

            $old = $product->toArray();
            $product->update($data);
            $product->categories()->sync($categories);

            AuditLog::log('updated', 'Product', $product->id, $old, $product->toArray());
        });

        return back()->with('success', 'Product information updated successfully.');
    }

    public function destroy(Product $product)
    {
        $id = $product->id;
        $name = $product->name;
        $product->delete();

        AuditLog::log('deleted', 'Product', $id);

        return redirect()->route('admin.products.index')
            ->with('success', "Product '{$name}' was moved to trash.");
    }

    /**
     * Upload an image to a specific colour gallery.
     */
    public function uploadImage(Request $request, Product $product)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
            'attribute_value_id' => 'nullable|exists:attribute_values,id',
            'alt_text' => 'nullable|string|max:255',
            'is_primary' => 'nullable|boolean',
        ]);

        $path = $request->file('image')->store('products', 'public');
        $isPrimary = $request->boolean('is_primary');

        if ($isPrimary) {
            // Unset previous primary image
            ProductImage::where('product_id', $product->id)->update(['is_primary' => false]);
        }

        $image = ProductImage::create([
            'product_id' => $product->id,
            'attribute_value_id' => $request->attribute_value_id ?: null,
            'image_path' => Storage::url($path),
            'alt_text' => $request->alt_text ?? $product->name,
            'sort_order' => ProductImage::where('product_id', $product->id)->max('sort_order') + 1,
            'is_primary' => $isPrimary,
        ]);

        return back()->with('success', 'Image successfully uploaded to colour gallery.');
    }

    /**
     * Delete an image from gallery.
     */
    public function deleteImage(ProductImage $image)
    {
        $productId = $image->product_id;
        $image->delete();

        return back()->with('success', 'Image removed from gallery.');
    }

    /**
     * Set an image as primary.
     */
    public function setPrimaryImage(ProductImage $image)
    {
        ProductImage::where('product_id', $image->product_id)->update(['is_primary' => false]);
        $image->is_primary = true;
        $image->save();

        return back()->with('success', 'Primary thumbnail updated.');
    }

    /**
     * Generate or Add Variant combinations.
     */
    public function storeVariant(Request $request, Product $product)
    {
        $data = $request->validate([
            'sku' => 'required|string|max:64|unique:product_variants,sku',
            'barcode' => 'nullable|string|max:64',
            'price_override' => 'nullable|numeric|min:0',
            'sale_price_override' => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'attribute_values' => 'required|array',
            'attribute_values.*' => 'exists:attribute_values,id',
        ]);

        DB::transaction(function () use ($data, $product) {
            $variant = ProductVariant::create([
                'product_id' => $product->id,
                'sku' => $data['sku'],
                'barcode' => $data['barcode'] ?? null,
                'price_override' => $data['price_override'] ?? null,
                'sale_price_override' => $data['sale_price_override'] ?? null,
                'status' => 'active',
            ]);

            $variant->attributeValues()->sync($data['attribute_values']);

            $inv = Inventory::create([
                'product_variant_id' => $variant->id,
                'quantity' => $data['stock_quantity'],
                'reserved_quantity' => 0,
                'low_stock_threshold' => 5,
            ]);

            if ($data['stock_quantity'] > 0) {
                InventoryMovement::create([
                    'inventory_id' => $inv->id,
                    'change_amount' => $data['stock_quantity'],
                    'previous_qty' => 0,
                    'new_qty' => $data['stock_quantity'],
                    'type' => 'purchase',
                    'admin_id' => auth('admin')->id(),
                    'notes' => 'Initial stock on variant creation',
                ]);
            }
        });

        return back()->with('success', 'New variant combination added successfully.');
    }

    /**
     * Update an individual variant.
     */
    public function updateVariant(Request $request, ProductVariant $variant)
    {
        $data = $request->validate([
            'sku' => "required|string|max:64|unique:product_variants,sku,{$variant->id}",
            'price_override' => 'nullable|numeric|min:0',
            'sale_price_override' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive',
            'stock_quantity' => 'required|integer|min:0',
        ]);

        DB::transaction(function () use ($data, $variant) {
            $variant->update([
                'sku' => $data['sku'],
                'price_override' => $data['price_override'] ?? null,
                'sale_price_override' => $data['sale_price_override'] ?? null,
                'status' => $data['status'],
            ]);

            $inv = $variant->inventory;
            if ($inv && $inv->quantity != $data['stock_quantity']) {
                $diff = $data['stock_quantity'] - $inv->quantity;
                $prev = $inv->quantity;
                $inv->quantity = $data['stock_quantity'];
                $inv->save();

                InventoryMovement::create([
                    'inventory_id' => $inv->id,
                    'change_amount' => $diff,
                    'previous_qty' => $prev,
                    'new_qty' => $inv->quantity,
                    'type' => 'manual_adjust',
                    'admin_id' => auth('admin')->id(),
                    'notes' => 'Updated via Product Editor',
                ]);
            }
        });

        return back()->with('success', "Variant {$variant->sku} updated successfully.");
    }

    /**
     * Delete a variant.
     */
    public function destroyVariant(ProductVariant $variant)
    {
        $variant->delete();
        return back()->with('success', 'Variant removed.');
    }

    /**
     * Duplicate a product.
     */
    public function duplicate(Product $product)
    {
        $newProduct = DB::transaction(function () use ($product) {
            $new = $product->replicate([
                'base_sku', 'slug', 'created_at', 'updated_at',
            ]);
            $new->name = "{$product->name} (Copy)";
            $new->slug = Str::slug($new->name) . '-' . substr(uniqid(), -4);
            $new->base_sku = $product->base_sku . '-COPY-' . strtoupper(substr(uniqid(), -3));
            $new->status = 'draft';
            $new->save();

            // Duplicate categories
            $new->categories()->sync($product->categories->pluck('id'));

            // Duplicate images
            foreach ($product->images as $img) {
                ProductImage::create([
                    'product_id' => $new->id,
                    'attribute_value_id' => $img->attribute_value_id,
                    'image_path' => $img->image_path,
                    'alt_text' => $img->alt_text,
                    'sort_order' => $img->sort_order,
                    'is_primary' => $img->is_primary,
                ]);
            }

            return $new;
        });

        return redirect()->route('admin.products.edit', $newProduct->id)
            ->with('success', 'Product duplicated as draft successfully.');
    }
}
