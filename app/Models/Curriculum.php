<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['program_id', 'year_level', 'semester_id'])]
class Curriculum extends Model
{
    protected $table = 'curriculums';
    /** @return BelongsTo<Program, $this> */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    /** @return BelongsTo<Semester, $this> */
    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    /** @return BelongsToMany<Subject, $this> */
    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'curriculum_subject')->withTimestamps();
    }
}
