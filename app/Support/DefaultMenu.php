<?php

namespace App\Support;

use App\Models\Branch;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Starter menu every new restaurant gets, so its storefront is not empty on day one.
 * Each category has a photo (public/assets/frontend/img/categories); items have none.
 * The photo is copied into storage so the owner can replace or delete it like any upload.
 */
class DefaultMenu
{
    /** image file => [category name, description, [[item, price], ...]] */
    public const MENU = [
        'noodles' => ['Noodles', 'Wok-tossed noodles with fresh vegetables.', [
            ['Veg. Fried Noodle', 550], ['Egg Fried Noodle', 570], ['Egg Veg. Fried Noodle', 590], ['Mixed Fried Noodle', 620],
        ]],
        'pizza' => ['Pizza', 'Stone-baked pizzas with melted cheese.', [
            ['Tomato Cheese Pizza', 690], ['Tomato Garlic Cheese Pizza', 690], ['Veg. Mushroom Tomato Cheese Pizza', 780],
            ['Mixed Pizza', 790], ['Oregano Cheese Pizza', 760], ['Tuna Cheese Pizza', 780], ['Tomato Cheese Mushroom Pizza', 780],
        ]],
        'macaroni' => ['Macaroni', 'Comforting macaroni in rich sauces.', [
            ['Tomato Sauce Macaroni', 560], ['Tomato Cheese Macaroni', 590], ['Veg. Cheese Macaroni', 590],
            ['Tuna Macaroni with Cheese', 630], ['Egg Veg. Cheese Macaroni', 630], ['Mixed Macaroni', 670],
        ]],
        'spaghetti' => ['Spaghetti', 'Classic spaghetti, cooked to order.', [
            ['Tomato Sauce Spaghetti', 560], ['Tomato Cheese Spaghetti', 590], ['Veg. Cheese Spaghetti', 590],
            ['Egg Veg. Cheese Spaghetti', 630], ['Tuna Cheese Spaghetti', 630], ['Mixed Spaghetti', 670],
        ]],
        'salad' => ['Salad', 'Light, fresh and healthy.', [
            ['Green Salad', 450], ['Boiled Vegetable', 450],
        ]],
        'momo' => ['Momo', 'Hand-folded dumplings served with achar.', [
            ['Veg. Momo', 580], ['Egg Veg. Cheese Momo', 640], ['Tuna Cheese Momo', 650], ['Veg. Cheese Momo', 610], ['Mixed Momo', 670],
        ]],
        'potatoes' => ['Potatoes', 'Fried, mashed and rosti potato favourites.', [
            ['Boiled Potatoes', 500], ['Plain Fried Potatoes', 540], ['Chips (French Fried)', 570], ['Mashed Potatoes with Cheese', 590],
            ['Plain Rosti', 530], ['Rosti with Cheese', 570], ['Rosti with Double Fried Egg', 700], ['Veg Fried Potatoes with Cheese', 600],
            ['Egg Veg. Fried Potatoes with Cheese', 680], ['Fried Potatoes with Cheese', 560],
            ['Fried Potatoes Cheese with 2 Fried Egg Top', 700], ['Chips with Fried Egg', 730],
        ]],
        'roll' => ['Roll', 'Crispy spring rolls served with chips.', [
            ['Veg. Spring Roll / Chips', 620], ['Egg Veg. Cheese Spring Roll / Chips', 650], ['Mixed Spring Roll / Chips', 690],
        ]],
        'dessert' => ['Dessert', 'Sweet endings to your meal.', [
            ['Rice Pudding', 450], ['Chocolate Pudding', 430], ['Custard Pudding', 430], ['Apple Roll', 440],
            ['Apple Roll with Custard', 500], ['Snickers or Mars Roll', 430], ['Snickers or Mars Roll with Custard', 500],
        ]],
    ];

    public static function seed(Branch $branch): void
    {
        $order = 0;

        foreach (self::MENU as $file => [$name, $description, $items]) {
            $category = MenuCategory::withoutGlobalScopes()->create([
                'branch_id'   => $branch->id,
                'name'        => $name,
                'description' => $description,
                'image'       => self::copyImage($file),
                'sort_order'  => $order++,
                'is_active'   => true,
            ]);

            foreach ($items as $i => [$itemName, $price]) {
                MenuItem::create([
                    'menu_category_id' => $category->id,
                    'name'             => $itemName,
                    'price'            => $price,
                    'is_available'     => true,
                    'sort_order'       => $i,
                ]);
            }
        }
    }

    /** Copy a bundled photo into public storage; returns its path, or null if it is missing. */
    private static function copyImage(string $file): ?string
    {
        $source = public_path("assets/frontend/img/categories/{$file}.jpg");
        if (! is_file($source)) {
            return null;
        }

        $path = 'menu-categories/' . $file . '-' . Str::lower(Str::random(10)) . '.jpg';

        return Storage::disk('public')->put($path, file_get_contents($source)) ? $path : null;
    }
}
