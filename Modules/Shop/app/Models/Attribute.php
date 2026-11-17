<?php

namespace Modules\Shop\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\Shop\Models\Concerns\BelongsToShop;

/** A product property (Color, Size, RAM...) offered by the categories it is attached to. */
class Attribute extends Model
{
    use BelongsToShop;

    public const INPUT_TYPES = ['select' => 'Select', 'color' => 'Color', 'text' => 'Text', 'number' => 'Number', 'email' => 'Email'];

    protected $connection = 'shop';

    protected $fillable = ['company_id', 'attribute_title', 'input_type'];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'attribute_category')->withTimestamps();
    }
}
