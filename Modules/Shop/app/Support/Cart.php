<?php

namespace Modules\Shop\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Modules\Shop\Models\Product;

/**
 * Storefront shopping cart, kept in the session per shop. Only product ids, quantities and the
 * chosen attributes are stored: names and prices always come from the database, so a shopper
 * can't change a price (multiple_pasal stored the whole product, price included, from a form field).
 */
class Cart
{
    private static function key(): string
    {
        return 'shop_cart.'.ShopTenancy::id();
    }

    /** @return array<string, array{product_id:int, quantity:int, attribute:array}> */
    public static function stored(): array
    {
        return session(self::key(), []);
    }

    public static function add(Product $product, int $quantity, array $attributes = []): void
    {
        $lines = self::stored();

        // Same product with the same options: add to that line.
        foreach ($lines as $id => $line) {
            if ($line['product_id'] === $product->id && $line['attribute'] == $attributes) {
                $lines[$id]['quantity'] += $quantity;
                session([self::key() => $lines]);

                return;
            }
        }

        $lines[Str::random(10)] = ['product_id' => $product->id, 'quantity' => $quantity, 'attribute' => $attributes];
        session([self::key() => $lines]);
    }

    public static function remove(string $id): void
    {
        $lines = self::stored();
        unset($lines[$id]);
        session([self::key() => $lines]);
    }

    public static function clear(): void
    {
        session()->forget(self::key());
    }

    /** Cart lines with their current product (lines whose product was deleted are dropped). */
    public static function lines(): Collection
    {
        $lines    = self::stored();
        $products = Product::with('productImage', 'category')->whereIn('id', array_column($lines, 'product_id'))->get()->keyBy('id');

        return collect($lines)
            ->filter(fn ($line) => isset($products[$line['product_id']]))
            ->map(fn ($line) => $line + ['product' => $products[$line['product_id']]]);
    }

    public static function quantityOf(int $productId): int
    {
        return (int) collect(self::stored())->where('product_id', $productId)->sum('quantity');
    }

    public static function total(): float
    {
        return (float) self::lines()->sum(fn ($line) => (float) $line['product']->price * $line['quantity']);
    }

    /**
     * The cart as JSON in the shape the storefront theme reads:
     * {id: {product: {id, name, price, image}, attribute: [...], quantity}}, with current prices.
     */
    public static function raw(): string
    {
        return json_encode(self::lines()->map(fn ($line) => [
            'product' => [
                'id'    => $line['product']->id,
                'name'  => $line['product']->name,
                'price' => (float) $line['product']->price,
                'image' => $line['product']->image_url ?: asset('shop/frontend/simple_theme/img/product-'.(($line['product']->id % 28) + 1).'.jpg'),
            ],
            'attribute' => $line['attribute'],
            'quantity'  => $line['quantity'],
        ])->all());
    }
}
