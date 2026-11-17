<?php

namespace Modules\School\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\School\Enums\SubjectTypeEnum;

class Subject extends Model
{
    protected $connection = 'school';

    protected $fillable = ['name', 'code', 'type'];

    protected function casts(): array
    {
        return ['type' => SubjectTypeEnum::class];
    }

    public function classrooms(): BelongsToMany
    {
        return $this->belongsToMany(ClassroomModel::class, 'classroom_subject', 'subject_id', 'classroom_id');
    }
}
