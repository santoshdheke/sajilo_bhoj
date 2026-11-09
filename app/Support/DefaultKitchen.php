<?php

namespace App\Support;

use App\Models\Branch;
use App\Models\KitchenColumn;

/**
 * Default Kitchen Display columns, in the order an order moves through them.
 * The slugs are the order statuses the rest of the app already uses
 * (website checkout creates 'pending', dashboards count 'preparing'/'ready'),
 * so only the names shown on the board are friendlier.
 * The colour also picks the column icon (warning = hourglass, indigo = flame, green = check).
 */
class DefaultKitchen
{
    public const COLUMNS = [
        ['name' => 'New Orders',     'slug' => 'pending',   'color' => 'warning'],
        ['name' => 'Accepted',       'slug' => 'confirmed', 'color' => 'orange'],
        ['name' => 'Cooking',        'slug' => 'preparing', 'color' => 'indigo'],
        ['name' => 'Ready to Serve', 'slug' => 'ready',     'color' => 'green'],
    ];

    public static function seed(Branch $branch): void
    {
        foreach (self::COLUMNS as $i => $col) {
            KitchenColumn::withoutGlobalScopes()->create($col + ['branch_id' => $branch->id, 'sort_order' => $i]);
        }
    }
}
