<?php

namespace App\Http\Requests\Cashier;

use App\Enums\AppPermission;
use App\Enums\CashierNotificationType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCashierNotificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can(AppPermission::ManageCashierNotifications->value) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'type' => ['required', Rule::enum(CashierNotificationType::class)],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:2000'],
        ];
    }
}
