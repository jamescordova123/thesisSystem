<?php

namespace App\Http\Requests\Admin;

use App\Enums\AppPermission;
use App\Enums\AppRole;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAnnouncementRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can(AppPermission::ManageAnnouncements->value) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:5000'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'send_to_all' => ['required', 'boolean'],
            'publish' => ['required', 'boolean'],
            'user_ids' => ['array'],
            'user_ids.*' => ['integer', 'exists:users,id'],
            'role_ids' => ['array'],
            'role_ids.*' => ['string', Rule::in(array_column(AppRole::options(), 'value'))],
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if (! $this->boolean('send_to_all')
                && empty($this->input('user_ids'))
                && empty($this->input('role_ids'))) {
                $validator->errors()->add(
                    'user_ids',
                    __('Select at least one recipient or send to all users.'),
                );
            }
        });
    }
}
