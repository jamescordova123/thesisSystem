<?php

namespace App\Http\Requests\Student;

use App\Enums\AppRole;
use App\Enums\StudentSex;
use App\Enums\StudentStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveStudentInformationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->hasRole(AppRole::Student->value) ?? false;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $nullableFields = [
            'father_name',
            'mother_maiden_name',
            'facebook_account',
            'facebook_profile',
            'last_school_attended',
            'previous_section',
            'last_school_address',
            'academic_year',
            'completion_date',
            'gwa',
        ];

        $merged = [];

        foreach ($nullableFields as $field) {
            if ($this->input($field) === '') {
                $merged[$field] = null;
            }
        }

        $this->merge($merged);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'school_year' => ['required', 'string', 'max:20'],
            'full_name' => ['required', 'string', 'max:255'],
            'academic_program' => ['required', 'string', 'max:255'],
            'status' => ['required', 'string', Rule::enum(StudentStatus::class)],
            'birthdate' => ['required', 'date', 'before:today'],
            'sex' => ['required', 'string', Rule::enum(StudentSex::class)],
            'age' => ['required', 'integer', 'min:1', 'max:120'],
            'place_of_birth' => ['required', 'string', 'max:255'],
            'current_address' => ['required', 'string', 'max:1000'],
            'permanent_address' => ['required', 'string', 'max:1000'],
            'father_name' => ['nullable', 'string', 'max:255'],
            'mother_maiden_name' => ['nullable', 'string', 'max:255'],
            'guardian_name' => ['required', 'string', 'max:255'],
            'primary_contact_number' => ['required', 'string', 'max:30', 'regex:/^[\d\s+\-()]+$/'],
            'facebook_account' => ['nullable', 'string', 'max:255'],
            'facebook_profile' => ['nullable', 'string', 'max:255'],
            'last_school_attended' => ['nullable', 'string', 'max:255'],
            'previous_section' => ['nullable', 'string', 'max:255'],
            'last_school_address' => ['nullable', 'string', 'max:1000'],
            'academic_year' => ['nullable', 'string', 'max:20'],
            'completion_date' => ['nullable', 'date'],
            'gwa' => ['nullable', 'numeric', 'min:0', 'max:5'],
        ];
    }
}
