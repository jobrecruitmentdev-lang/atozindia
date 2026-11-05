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
        $rootCategories = Category::where('is_active', true)
            ->whereNull('parent_id')
            ->with(['children' => fn($q) => $q->where('is_active', true)->orderBy('name')])
            ->orderBy('name')
            ->get();
        $attributes = Attribute::with('values')->get();

        return view('admin.products.create', compact('brands', 'categories', 'rootCategories', 'attributes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'base_sku' => 'nullable|string|max:64|unique:products,base_sku',
            'barcode' => 'nullable|string|max:64',
            'brand_id' => 'nullable|exists:brands,id',
            'categories' => 'required|array|min:1',
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
            'primary_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ], [
            'name.required' => 'Please enter a product name.',
            'categories.required' => 'Please select at least one department or category.',
            'price.required' => 'Please specify the regular price in INR ₹.',
        ]);

        if (empty($data['base_sku'])) {
            $data['base_sku'] = 'ATOZ-' . strtoupper(Str::random(7));
        }

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
            $primaryImage = $data['primary_image'] ?? null;
            unset($data['primary_image']);

            $product = Product::create($data);
            $product->categories()->sync($categories);

            // 1. Process multi-color swatches with multi-pose images and variants (Availsmart style)
            if ($request->filled('color_count') && (int)$request->input('color_count') > 0) {
                $this->processColorSwatches($request, $product);
            }

            // 2. If single fallback primary image was uploaded
            if ($request->hasFile('primary_image') && !$product->images()->exists()) {
                $path = $request->file('primary_image')->store('products', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => Storage::url($path),
                    'alt_text' => $product->name,
                    'is_primary' => true,
                    'sort_order' => 1,
                ]);
            }

            // 3. If no variants were created via color swatches and product is not variable, create single default variant
            if (!$product->is_variable && !$product->variants()->exists()) {
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
            ->with('success', "Product '{$product->name}' created successfully!");
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
        $rootCategories = Category::where('is_active', true)
            ->whereNull('parent_id')
            ->with(['children' => fn($q) => $q->where('is_active', true)->orderBy('name')])
            ->orderBy('name')
            ->get();
        $attributes = Attribute::with('values')->get();
        $currentStock = $product->variants->first()?->inventory?->quantity ?? 0;

        $colorAttr = Attribute::where('code', 'color')->first();
        $productColors = [];

        // Group existing images by color
        foreach ($product->images as $img) {
            $cVal = $img->colorValue;
            $cId = $cVal ? $cVal->id : 0;
            $cName = $cVal ? $cVal->value : 'Standard / Global';
            $cHex = ($cVal && $cVal->color_hex) ? $cVal->color_hex : '#111110';

            if (!isset($productColors[$cId])) {
                $productColors[$cId] = [
                    'id' => $cId,
                    'name' => $cName,
                    'hex' => $cHex,
                    'existing_images' => [],
                    'new_previews' => [],
                    'variants' => [],
                ];
            }

            $productColors[$cId]['existing_images'][] = [
                'id' => $img->id,
                'url' => $img->image_path,
                'is_primary' => (bool) $img->is_primary,
            ];
        }

        // Attach variants to their respective colors
        foreach ($product->variants as $variant) {
            $cVal = $colorAttr ? $variant->attributeValues->firstWhere('attribute_id', $colorAttr->id) : null;
            $sVal = $colorAttr ? $variant->attributeValues->first(fn($v) => $v->attribute_id !== $colorAttr->id) : $variant->attributeValues->first();

            $cId = $cVal ? $cVal->id : 0;
            $cName = $cVal ? $cVal->value : 'Standard / Global';
            $cHex = ($cVal && $cVal->color_hex) ? $cVal->color_hex : '#111110';

            if (!isset($productColors[$cId])) {
                $productColors[$cId] = [
                    'id' => $cId,
                    'name' => $cName,
                    'hex' => $cHex,
                    'existing_images' => [],
                    'new_previews' => [],
                    'variants' => [],
                ];
            }

            $productColors[$cId]['variants'][] = [
                'id' => $variant->id,
                'size' => $sVal ? $sVal->value : '',
                'sku' => $variant->sku,
                'price' => $variant->price_override ? (string) $variant->price_override : '',
                'stock' => $variant->inventory ? $variant->inventory->quantity : 0,
            ];
        }

        if (empty($productColors)) {
            $productColors[] = [
                'id' => null,
                'name' => 'Obsidian Black',
                'hex' => '#111110',
                'existing_images' => [],
                'new_previews' => [],
                'variants' => [
                    ['id' => null, 'size' => 'Standard', 'sku' => '', 'price' => '', 'stock' => 10],
                ],
            ];
        }

        $productColorsData = array_values($productColors);

        return view('admin.products.edit', compact(
            'product',
            'brands',
            'categories',
            'rootCategories',
            'attributes',
            'currentStock',
            'productColorsData'
        ));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'base_sku' => "required|string|max:64|unique:products,base_sku,{$product->id}",
            'barcode' => 'nullable|string|max:64',
            'brand_id' => 'nullable|exists:brands,id',
            'categories' => 'required|array|min:1',
            'categories.*' => 'exists:categories,id',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'stock_quantity' => 'nullable|integer|min:0',
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

        DB::transaction(function () use ($data, $product, $request) {
            $categories = $data['categories'];
            unset($data['categories']);

            $stockQty = $request->input('stock_quantity');
            unset($data['stock_quantity']);

            $old = $product->toArray();
            $product->update($data);
            $product->categories()->sync($categories);

            // 1. Delete selected images
            if ($request->has('delete_images') && is_array($request->delete_images)) {
                ProductImage::where('product_id', $product->id)->whereIn('id', $request->delete_images)->delete();
            }

            // 2. Delete selected variants
            if ($request->has('delete_variants') && is_array($request->delete_variants)) {
                ProductVariant::where('product_id', $product->id)->whereIn('id', $request->delete_variants)->delete();
            }

            // 3. Process multi-color swatches with multi-pose images and variants (Availsmart style)
            if ($request->filled('color_count') && (int)$request->input('color_count') > 0) {
                $this->processColorSwatches($request, $product);
            }

            // 4. If simple product, update single default variant stock directly
            if (!$product->is_variable && $stockQty !== null) {
                $defaultVariant = $product->variants()->first();
                if (!$defaultVariant) {
                    $defaultVariant = ProductVariant::create([
                        'product_id' => $product->id,
                        'sku' => $product->base_sku,
                        'status' => 'active',
                    ]);
                }

                $inv = $defaultVariant->inventory;
                if ($inv) {
                    $oldQty = $inv->quantity;
                    $inv->update(['quantity' => (int) $stockQty]);
                    if ($oldQty != $stockQty) {
                        InventoryMovement::create([
                            'inventory_id' => $inv->id,
                            'change_amount' => $stockQty - $oldQty,
                            'previous_qty' => $oldQty,
                            'new_qty' => (int) $stockQty,
                            'type' => 'manual_adjust',
                            'admin_id' => auth('admin')->id(),
                            'notes' => 'Updated via Product Quick Edit',
                        ]);
                    }
                } else {
                    Inventory::create([
                        'product_variant_id' => $defaultVariant->id,
                        'quantity' => (int) $stockQty,
                        'reserved_quantity' => 0,
                        'low_stock_threshold' => 5,
                    ]);
                }
            }

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
        // Filter out empty attribute selects (e.g. if only Size or only Colour is selected)
        $rawValues = array_values(array_filter((array) $request->input('attribute_values', []), fn($v) => !empty($v)));
        $request->merge(['attribute_values' => $rawValues]);

        $data = $request->validate([
            'sku' => 'nullable|string|max:64|unique:product_variants,sku',
            'barcode' => 'nullable|string|max:64',
            'price_override' => 'nullable|numeric|min:0',
            'sale_price_override' => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'attribute_values' => 'required|array|min:1',
            'attribute_values.*' => 'exists:attribute_values,id',
        ], [
            'attribute_values.required' => 'Please select at least one attribute option (e.g. Size or Colour) for this variant.',
            'attribute_values.min' => 'Please select at least one attribute option (e.g. Size or Colour) for this variant.',
        ]);

        if (empty($data['sku'])) {
            $data['sku'] = $product->base_sku . '-' . strtoupper(Str::random(4));
        }

        DB::transaction(function () use ($data, $product) {
            // Automatically mark product as variable when adding custom variants
            if (!$product->is_variable) {
                $product->update(['is_variable' => true]);
            }

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

    /**
     * Process dynamic multi-color sets, multiple images per color, and size variants (Availsmart Style).
     */
    protected function processColorSwatches(Request $request, Product $product): void
    {
        $colorCount = (int) $request->input('color_count', 0);
        if ($colorCount <= 0) {
            return;
        }

        $colorAttr = Attribute::firstOrCreate(['code' => 'color'], [
            'name' => 'Colour',
            'type' => 'color',
        ]);

        $sizeAttr = Attribute::firstOrCreate(['code' => 'size'], [
            'name' => 'Size',
            'type' => 'button',
        ]);

        $hasPrimaryImage = $product->images()->where('is_primary', true)->exists();
        $totalVariantsCreated = 0;

        for ($cIdx = 0; $cIdx < $colorCount; $cIdx++) {
            $colorName = trim($request->input("color_name_{$cIdx}", ''));
            $colorHex = trim($request->input("color_hex_{$cIdx}", '#111110'));

            if (empty($colorName)) {
                continue;
            }

            // 1. Find or create Color Attribute Value
            $colorVal = AttributeValue::firstOrCreate(
                ['attribute_id' => $colorAttr->id, 'value' => $colorName],
                ['color_hex' => $colorHex, 'sort_order' => $cIdx + 1]
            );
            if ($colorHex && $colorVal->color_hex !== $colorHex) {
                $colorVal->update(['color_hex' => $colorHex]);
            }

            // 2. Set existing primary image if selected via radio
            $existingPrimaryId = $request->input("existing_primary_{$cIdx}");
            if (!$existingPrimaryId && $colorVal) {
                $existingPrimaryId = $request->input("existing_primary_{$colorVal->id}");
            }
            if ($existingPrimaryId) {
                ProductImage::where('product_id', $product->id)->where('attribute_value_id', $colorVal->id)->update(['is_primary' => false]);
                ProductImage::where('id', $existingPrimaryId)->where('product_id', $product->id)->update(['is_primary' => true]);
                $hasPrimaryImage = true;
            }

            // 3. Upload multiple images for THIS color (Multi-Pose support)
            $uploadedImages = $request->file("color_images_{$cIdx}") ?? [];
            if (!is_array($uploadedImages) && $uploadedImages) {
                $uploadedImages = [$uploadedImages];
            }

            $primaryImgIdx = (int) $request->input("primary_image_{$cIdx}", 0);

            foreach ($uploadedImages as $imgOrder => $file) {
                if ($file && $file->isValid()) {
                    $path = $file->store('products', 'public');
                    $isPrim = (!$hasPrimaryImage && $imgOrder === $primaryImgIdx);
                    if ($isPrim) {
                        $hasPrimaryImage = true;
                    }

                    ProductImage::create([
                        'product_id' => $product->id,
                        'attribute_value_id' => $colorVal->id,
                        'image_path' => Storage::url($path),
                        'alt_text' => "{$product->name} - {$colorName} (Pose " . ($imgOrder + 1) . ")",
                        'is_primary' => $isPrim,
                        'sort_order' => $imgOrder + 1,
                    ]);
                }
            }

            // 4. Handle Size Variants under this color
            $vIds = $request->input("variant_id_{$cIdx}", []);
            $vSizes = $request->input("variant_size_{$cIdx}", []);
            $vSkus = $request->input("variant_sku_{$cIdx}", []);
            $vPrices = $request->input("variant_price_{$cIdx}", []);
            $vStocks = $request->input("variant_stock_{$cIdx}", []);

            if (!empty($vSizes) && is_array($vSizes)) {
                foreach ($vSizes as $idx => $sizeStr) {
                    $sizeStr = trim($sizeStr);
                    $skuVal = isset($vSkus[$idx]) ? trim($vSkus[$idx]) : '';
                    $pVal = isset($vPrices[$idx]) && $vPrices[$idx] !== '' ? (float) $vPrices[$idx] : null;
                    $stVal = isset($vStocks[$idx]) && $vStocks[$idx] !== '' ? (int) $vStocks[$idx] : 10;
                    $vId = isset($vIds[$idx]) ? (int) $vIds[$idx] : null;

                    if (empty($skuVal)) {
                        $cleanSize = $sizeStr ? Str::slug($sizeStr) : 'STD';
                        $skuVal = strtoupper("{$product->base_sku}-" . Str::slug($colorName) . "-{$cleanSize}");
                        $uniqueSku = $skuVal;
                        $counter = 1;
                        while (ProductVariant::where('sku', $uniqueSku)->where('id', '!=', $vId)->exists()) {
                            $uniqueSku = "{$skuVal}-" . $counter++;
                        }
                        $skuVal = $uniqueSku;
                    }

                    $attrValueIds = [$colorVal->id];
                    if (!empty($sizeStr)) {
                        $sizeVal = AttributeValue::firstOrCreate(
                            ['attribute_id' => $sizeAttr->id, 'value' => $sizeStr],
                            ['sort_order' => 1]
                        );
                        $attrValueIds[] = $sizeVal->id;
                    }

                    if ($vId) {
                        $variant = ProductVariant::where('id', $vId)->where('product_id', $product->id)->first();
                        if ($variant) {
                            $variant->update([
                                'sku' => $skuVal,
                                'price_override' => $pVal,
                            ]);
                            $variant->attributeValues()->sync($attrValueIds);
                            if ($variant->inventory) {
                                $variant->inventory->update(['quantity' => $stVal]);
                            } else {
                                Inventory::create([
                                    'product_variant_id' => $variant->id,
                                    'quantity' => $stVal,
                                    'reserved_quantity' => 0,
                                    'low_stock_threshold' => 5,
                                ]);
                            }
                            $totalVariantsCreated++;
                        }
                    } else {
                        $variant = ProductVariant::create([
                            'product_id' => $product->id,
                            'sku' => $skuVal,
                            'price_override' => $pVal,
                            'status' => 'active',
                        ]);
                        $variant->attributeValues()->sync($attrValueIds);
                        Inventory::create([
                            'product_variant_id' => $variant->id,
                            'quantity' => $stVal,
                            'reserved_quantity' => 0,
                            'low_stock_threshold' => 5,
                        ]);
                        $totalVariantsCreated++;
                    }
                }
            }
        }

        // If product has no primary image marked, set the first available image as primary
        if (!$product->images()->where('is_primary', true)->exists()) {
            $firstImg = $product->images()->first();
            if ($firstImg) {
                $firstImg->update(['is_primary' => true]);
            }
        }

        // Automatically set is_variable = true if multiple colors or variants exist
        if ($colorCount > 1 || $totalVariantsCreated > 1) {
            $product->update(['is_variable' => true]);
        }
    }
}
