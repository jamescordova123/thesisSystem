<?php

namespace App\Http\Requests\Registrar;

use App\Enums\AppPermission;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can(AppPermission::ManageSections->value) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required', 'string', 'max:100',
                Rule::unique('sections')->where(fn ($q) => $q
                    ->where('program_id', $this->input('program_id'))
                    ->where('year_level', $this->input('year_level'))
                    ->where('semester_id', $this->input('semester_id'))
                    ->where('academic_year_id', $this->input('academic_year_id'))),
            ],
            'program_id' => ['required', 'integer', 'exists:programs,id'],
            'year_level' => ['required', 'integer', 'min:1', 'max:6'],
            'semester_id' => ['required', 'integer', 'exists:semesters,id'],
            'academic_year_id' => ['required', 'integer', 'exists:academic_years,id'],
            'adviser_id' => ['nullable', 'integer', 'exists:users,id'],
            'max_capacity' => ['required', 'integer', 'min:1', 'max:200'],
        ];
    }
}
