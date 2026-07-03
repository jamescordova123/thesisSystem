<?php

namespace App\Http\Requests\Registrar;

use App\Enums\AppPermission;
use App\Enums\EnrollmentStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEnrollmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can(AppPermission::ManageEnrollments->value) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'academic_year_id' => ['required', 'integer', 'exists:academic_years,id'],
            'semester_id' => ['required', 'integer', 'exists:semesters,id'],
            'program_id' => ['required', 'integer', 'exists:programs,id'],
            'year_level' => ['required', 'integer', 'min:1', 'max:6'],
            'section_id' => ['nullable', 'integer', 'exists:sections,id'],
            'status' => ['required', Rule::enum(EnrollmentStatus::class)],
            'enrolled_at' => ['nullable', 'date'],
            'remarks' => ['nullable', 'string', 'max:500'],
            'subject_ids' => ['nullable', 'array'],
            'subject_ids.*' => ['integer', 'exists:subjects,id'],
        ];
    }
}
