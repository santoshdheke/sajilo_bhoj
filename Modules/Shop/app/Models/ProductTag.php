<?php

namespace Modules\Shop\Models;

use Illuminate\Database\Eloquent\Model;

class ProductTag extends Model
{
    protected $connection = 'shop';

    protected $fillable = ['product_id', 'title'];
}
