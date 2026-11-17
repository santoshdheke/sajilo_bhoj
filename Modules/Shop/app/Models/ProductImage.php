<?php

namespace Modules\Shop\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProductImage extends Model
{
    protected $connection = 'shop';

    protected $fillable = ['product_id', 'image'];

    public function getImageUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->image);
    }

    public function getImagePathAttribute(): string
    {
        return $this->image_url;
    }
}
