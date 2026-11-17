<?php

namespace Modules\Shop\Models;

use Illuminate\Database\Eloquent\Model;

/** Quantity price tier: price_type "price_per_order", min_qty..max_qty => price. */
class ProductPrice extends Model
{
    protected $connection = 'shop';

    protected $fillable = ['product_id', 'price_type', 'attribute_json', 'min_qty', 'max_qty', 'price'];
}
