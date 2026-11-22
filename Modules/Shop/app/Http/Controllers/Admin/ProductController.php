<?php

namespace Modules\Shop\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Modules\Shop\Models\Brand;
use Modules\Shop\Models\Category;
use Modules\Shop\Models\Product;
use Modules\Shop\Models\ProductAttribute;
use Modules\Shop\Models\ProductAttributeValue;
use Modules\Shop\Models\ProductImage;
use Modules\Shop\Models\ProductPrice;
use Modules\Shop\Models\ProductTag;
use Modules\Shop\Support\ProductOptions;
use Modules\Shop\Support\ShopTenancy;

/**
 * Products (multiple_pasal's admin ProductController): details, category + sub-category,
 * brand, attribute values, tags, quantity price tiers and images. Stock comes from purchases.
 */
class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $products = Product::with('productImage', 'category', 'brand', 'purchaseOrderItems', 'stockOutLines')
            ->when($request->search, fn ($q, $s) => $q->where(fn ($q) => $q->where('name', 'like', "%{$s}%")
                ->orWhere('sku', 'like', "%{$s}%")->orWhere('barcode', 'like', "%{$s}%")))
            ->when($request->category_id, fn ($q, $c) => $q->where('category_id', $c))
            ->latest()->paginate($request->integer('items', 15) ?: 15)->withQueryString();

        return view('shop::admin.theme_one.product.index', [
            'products'   => $products,
            'categories' => Category::orderBy('title')->pluck('title', 'id'),
        ]);
    }

    public function create(): View
    {
        return view('shop::admin.theme_one.product.form', $this->formData(new Product(['minimum_stock' => 5, 'tax_rate' => 0])));
    }

    public function edit(Product $product): View
    {
        $product->load('productImages', 'productTags', 'productPricePerOrders', 'category');

        return view('shop::admin.theme_one.product.form', $this->formData($product));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        $product = DB::connection('shop')->transaction(function () use ($request, $data) {
            $product = Product::create($data);
            $this->saveExtras($request, $product);

            return $product;
        });

        return $request->boolean('back')
            ? redirect()->route('shop.admin.product.create')->with('success', "Product \"{$product->name}\" added. Add the next one.")
            : redirect()->route('shop.admin.product.index')->with('success', 'Product added.');
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $this->validated($request, $product);

        DB::connection('shop')->transaction(function () use ($request, $product, $data) {
            $product->update($data);
            $this->saveExtras($request, $product);
        });

        return redirect()->route('shop.admin.product.index')->with('success', 'Product updated.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        if ($product->orderProducts()->exists() || $product->purchaseOrderItems()->exists()) {
            return back()->with('error', "\"{$product->name}\" has sales or purchases, so it can't be deleted.");
        }

        $images = $product->productImages()->pluck('image');
        $product->delete();
        Storage::disk('public')->delete($images->all());

        return back()->with('success', 'Product deleted.');
    }

    /** Attribute inputs of a category (loaded into the product form when the category changes). */
    public function attributes(Request $request, Category $category): View
    {
        $values = [];
        if ($request->filled('product_id') && $product = Product::find($request->product_id)) {
            $values = ProductAttribute::with('productAttributeValues')->where('product_id', $product->id)->get()
                ->mapWithKeys(fn ($pa) => [$pa->attribute_id => $pa->productAttributeValues->pluck('value')->implode(',')])->all();
        }

        return view('shop::admin.theme_one.product._attributes', [
            'attributes' => $category->attributes()->orderBy('attribute_title')->get(),
            'values'     => $values,
        ]);
    }

    private function formData(Product $product): array
    {
        // Pre-select parent + sub category: a product's category may be a root or a child.
        $category = $product->category;
        $parentId = $category?->parent_id ? $category->parent_id : $category?->id;
        $childId  = $category?->parent_id ? $category->id : null;

        return [
            'product'    => $product,
            'roots'      => Category::where('parent_id', 0)->orderBy('title')->pluck('title', 'id'),
            'children'   => $parentId ? Category::where('parent_id', $parentId)->orderBy('title')->pluck('title', 'id') : collect(),
            'parentId'   => old('parent_category_id', $parentId),
            'childId'    => old('category_id', $childId),
            'brands'     => Brand::orderBy('name')->pluck('name', 'id'),
        ];
    }

    private function validated(Request $request, ?Product $product = null): array
    {
        $shop     = ShopTenancy::id();
        $unique   = fn (string $column) => Rule::unique('shop.products', $column)->where('company_id', $shop)->ignore($product?->id);
        $category = Rule::exists('shop.categories', 'id')->where('company_id', $shop);

        $data = $request->validate([
            'name'                       => ['required', 'string', 'max:255'],
            'slug'                       => ['required', 'string', 'max:255', 'regex:/^[a-z0-9-]+$/', $unique('slug')],
            'sku'                        => ['nullable', 'string', 'max:100'],
            'barcode'                    => ['nullable', 'string', 'max:100'],
            'parent_category_id'         => ['required', $category],
            'category_id'                => ['nullable', $category],
            'brand_id'                   => ['required', Rule::exists('shop.brands', 'id')->where('company_id', $shop)],
            'price'                      => ['nullable', 'numeric', 'min:0', 'max:100000000'],
            'cost_price'                 => ['nullable', 'numeric', 'min:0', 'max:100000000'],
            'tax_rate'                   => ['nullable', 'numeric', 'min:0', 'max:100'],
            'unit'                       => ['nullable', 'string', 'max:30'],
            'minimum_stock'              => ['nullable', 'integer', 'min:0'],
            'expiry_date'                => ['nullable', 'date'],
            'description'                => ['nullable', 'string'],
            'long_description'           => ['nullable', 'string'],
            'tags'                       => ['nullable', 'string', 'max:1000'],
            'product_image'              => [$product ? 'nullable' : 'required', 'array'],
            'product_image.*'            => ['image', 'mimes:jpeg,png,jpg,gif,webp', 'max:4096'],
            'attributes'                 => ['nullable', 'array'],
            'attributes.*'               => ['nullable', 'string', 'max:500'],
            'price_per_order'            => ['nullable', 'array'],
            'price_per_order.*.min_qty'  => ['required_with:price_per_order', 'integer', 'min:1'],
            'price_per_order.*.max_qty'  => ['nullable', 'integer', 'gte:price_per_order.*.min_qty'],
            'price_per_order.*.price'    => ['required_with:price_per_order', 'numeric', 'min:0'],
            'custom_fields'              => ['nullable', 'array', 'max:'.ProductOptions::MAX_FIELDS],
            'custom_fields.*.label'      => ['nullable', 'string', 'max:100'],
            'custom_fields.*.type'       => ['required_with:custom_fields.*.label', Rule::in(array_keys(ProductOptions::TYPES))],
            'custom_fields.*.options'    => ['nullable', 'array', 'max:50'],
            'custom_fields.*.options.*'  => ['nullable', 'string', 'max:100'],
            'custom_fields.*.placeholder' => ['nullable', 'string', 'max:150'],
            'custom_fields.*.required'   => ['nullable', 'boolean'],
        ], [
            'product_image.required' => 'Add at least one product image.',
            'brand_id.required'      => 'Choose a brand (use "Local" for unbranded goods).',
        ]);

        // The sub-category is optional: a product without one sits in the chosen parent category.
        $data['category_id']   = ($data['category_id'] ?? null) ?: $data['parent_category_id'];
        $data['tax_rate']      = $data['tax_rate'] ?? 0;
        $data['minimum_stock'] = $data['minimum_stock'] ?? 5;
        $data['custom_fields'] = ProductOptions::fromAdminInput($data['custom_fields'] ?? []) ?: null;

        return collect($data)->except(['parent_category_id', 'tags', 'product_image', 'attributes', 'price_per_order'])->all();
    }

    /** Tags, attribute values, price tiers and images (replaces what the form no longer has). */
    private function saveExtras(Request $request, Product $product): void
    {
        // Tags: comma separated.
        ProductTag::where('product_id', $product->id)->delete();
        collect(explode(',', (string) $request->input('tags')))->map(fn ($t) => trim($t))->filter()->unique()
            ->each(fn ($t) => ProductTag::create(['product_id' => $product->id, 'title' => $t]));

        // Attribute values: attributes[attribute_id] = "red,blue".
        $keep = [];
        foreach ((array) $request->input('attributes', []) as $attributeId => $value) {
            $values = collect(explode(',', (string) $value))->map(fn ($v) => trim($v))->filter()->unique();
            if ($values->isEmpty()) continue;

            $pa = ProductAttribute::updateOrCreate(['product_id' => $product->id, 'attribute_id' => $attributeId]);
            $keep[] = $pa->id;
            $pa->productAttributeValues()->whereNotIn('value', $values)->delete();
            $values->each(fn ($v) => ProductAttributeValue::firstOrCreate(['product_attribute_id' => $pa->id, 'value' => $v]));
        }
        ProductAttribute::where('product_id', $product->id)->whereNotIn('id', $keep)->delete();

        // Quantity price tiers.
        ProductPrice::where('product_id', $product->id)->where('price_type', 'price_per_order')->delete();
        if ($request->boolean('price_per_order_checkbox')) {
            foreach ((array) $request->input('price_per_order', []) as $tier) {
                ProductPrice::create([
                    'product_id' => $product->id,
                    'price_type' => 'price_per_order',
                    'min_qty'    => $tier['min_qty'],
                    'max_qty'    => ($tier['max_qty'] ?? null) ?: null,
                    'price'      => $tier['price'],
                ]);
            }
        }

        // Images: keep the ones still listed in old_image[], add new uploads.
        $kept = (array) $request->input('old_image', []);
        $removed = ProductImage::where('product_id', $product->id)->whereNotIn('image', $kept)->get();
        Storage::disk('public')->delete($removed->pluck('image')->all());
        ProductImage::whereIn('id', $removed->pluck('id'))->delete();

        foreach ((array) $request->file('product_image', []) as $file) {
            ProductImage::create([
                'product_id' => $product->id,
                'image'      => $file->store('shop/'.ShopTenancy::id().'/products/'.$product->id, 'public'),
            ]);
        }
    }
}
