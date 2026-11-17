<?php

namespace Modules\Shop\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SectionItem extends Model
{
    protected $connection = 'shop';

    protected $fillable = ['section_id', 'sectionable_type', 'sectionable_id'];

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function sectionable(): MorphTo
    {
        return $this->morphTo();
    }
}
