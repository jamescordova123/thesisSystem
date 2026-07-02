<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property string $school_year
 * @property string $full_name
 * @property string $academic_program
 * @property string $status
 * @property Carbon $birthdate
 * @property string $sex
 * @property int $age
 * @property string $place_of_birth
 * @property string $current_address
 * @property string $permanent_address
 * @property string|null $father_name
 * @property string|null $mother_maiden_name
 * @property string $guardian_name
 * @property string $primary_contact_number
 * @property string|null $facebook_account
 * @property string|null $facebook_profile
 * @property string|null $last_school_attended
 * @property string|null $previous_section
 * @property string|null $last_school_address
 * @property string|null $academic_year
 * @property Carbon|null $completion_date
 * @property string|null $gwa
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $user
 */
#[Fillable([
    'user_id',
    'school_year',
    'full_name',
    'academic_program',
    'status',
    'birthdate',
    'sex',
    'age',
    'place_of_birth',
    'current_address',
    'permanent_address',
    'father_name',
    'mother_maiden_name',
    'guardian_name',
    'primary_contact_number',
    'facebook_account',
    'facebook_profile',
    'last_school_attended',
    'previous_section',
    'last_school_address',
    'academic_year',
    'completion_date',
    'gwa',
])]
class StudentInformation extends Model
{
    /**
     * Get the user that owns the information sheet.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'birthdate' => 'date',
            'completion_date' => 'date',
            'age' => 'integer',
            'gwa' => 'decimal:2',
        ];
    }
}
