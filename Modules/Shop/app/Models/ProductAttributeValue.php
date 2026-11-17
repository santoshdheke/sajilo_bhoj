<?php

namespace Modules\Shop\Models;

use Illuminate\Database\Eloquent\Model;

class ProductAttributeValue extends Model
{
    protected $connection = 'shop';

    protected $fillable = ['product_attribute_id', 'value'];
}
