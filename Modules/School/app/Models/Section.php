<?php

namespace Modules\School\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Section extends Model
{
    protected $connection = 'school';

    protected $fillable = ['classroom_id', 'name', 'capacity', 'class_teacher_id'];

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(ClassroomModel::class, 'classroom_id');
    }

    public function classTeacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'class_teacher_id');
    }
}
