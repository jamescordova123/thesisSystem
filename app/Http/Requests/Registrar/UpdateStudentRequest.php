<?php

namespace App\Http\Requests\Registrar;

use App\Enums\AppPermission;
use App\Enums\EnrollmentStatus;
use App\Enums\StudentSex;
use App\Enums\StudentStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can(AppPermission::ManageStudents->value) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $userId = $this->route('user')?->id;

        return [
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'school_year' => ['required', 'string', 'max:20'],
            'program_id' => ['required', 'integer', 'exists:programs,id'],
            'section_id' => ['nullable', 'integer', 'exists:sections,id'],
            'year_level' => ['required', 'integer', 'min:1', 'max:6'],
            'enrollment_status' => ['required', Rule::enum(EnrollmentStatus::class)],
            'status' => ['required', Rule::enum(StudentStatus::class)],
            'birthdate' => ['required', 'date', 'before:today'],
            'sex' => ['required', Rule::enum(StudentSex::class)],
            'age' => ['required', 'integer', 'min:1', 'max:120'],
            'place_of_birth' => ['required', 'string', 'max:255'],
            'current_address' => ['required', 'string', 'max:1000'],
            'permanent_address' => ['required', 'string', 'max:1000'],
            'guardian_name' => ['required', 'string', 'max:255'],
            'primary_contact_number' => ['required', 'string', 'max:30'],
            'father_name' => ['nullable', 'string', 'max:255'],
            'mother_maiden_name' => ['nullable', 'string', 'max:255'],
        ];
    }
}
