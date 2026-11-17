<?php

namespace Modules\School\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\School\Enums\ClassroomStatusEnum;

/**
 * A school's class/grade (table `classrooms`). Named ClassroomModel because
 * "Class" is a reserved PHP word. Always school-scoped (company_id).
 */
class ClassroomModel extends Model
{
    protected $connection = 'school';

    protected $table = 'classrooms';

    protected $fillable = ['name', 'display_order', 'status'];

    protected function casts(): array
    {
        return ['status' => ClassroomStatusEnum::class];
    }

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class, 'classroom_id');
    }

    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'classroom_subject', 'classroom_id', 'subject_id');
    }
}
