<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id', 'academic_year_id', 'semester_id', 'subject_code', 'subject_name',
    'grade', 'gpa', 'gwa', 'academic_standing',
])]
class AcademicHistory extends Model
{
    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsTo<AcademicYear, $this> */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /** @return BelongsTo<Semester, $this> */
    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    protected function casts(): array
    {
        return [
            'grade' => 'decimal:2',
            'gpa' => 'decimal:2',
            'gwa' => 'decimal:2',
        ];
    }
}
