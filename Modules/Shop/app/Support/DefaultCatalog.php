<?php

namespace Modules\Shop\Support;

use Modules\Shop\Models\Attribute;
use Modules\Shop\Models\Brand;
use Modules\Shop\Models\Category;

/**
 * Starter catalog for a new shop (multiple_pasal's CompanyHelper::dataInsert, without the
 * storefront banners): common attributes, categories and brands to edit or delete.
 */
class DefaultCatalog
{
    public static function seed(int $companyId): void
    {
        $attributes = collect([
            ['Color', 'color'], ['Size', 'text'], ['RAM', 'text'], ['Storage', 'text'],
        ])->mapWithKeys(fn ($a) => [$a[0] => Attribute::create([
            'company_id' => $companyId, 'attribute_title' => $a[0], 'input_type' => $a[1],
        ])->id]);

        $categories = [
            'Apparels & Accessories'  => 'apparels-accessories',
            'Automobiles'             => 'automobiles',
            'Beauty & Health'         => 'beauty-health',
            'Computers'               => 'computers',
            'Books & Learning'        => 'books-learning',
            'Business & Industrial'   => 'business-industrial',
            'Electronics, TVs & More' => 'electronics-tvs-more',
        ];
        foreach ($categories as $title => $slug) {
            $category = Category::create(['company_id' => $companyId, 'title' => $title, 'slug' => $slug, 'parent_id' => 0]);

            if ($slug === 'computers') {
                $category->attributes()->sync([$attributes['Color'], $attributes['RAM'], $attributes['Storage']]);
            }
        }

        foreach (['Local' => 'local', 'Nike' => 'nike', 'Adidas' => 'adidas', 'Puma' => 'puma'] as $name => $slug) {
            Brand::create(['company_id' => $companyId, 'name' => $name, 'slug' => $slug, 'status' => true]);
        }
    }
}
