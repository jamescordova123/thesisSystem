<?php

namespace App\Http\Requests\Cashier;

use App\Enums\AppPermission;
use App\Enums\PaymentMethod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can(AppPermission::ManagePayments->value) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $paymentId = $this->route('payment')?->id;

        return [
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_method' => ['required', Rule::enum(PaymentMethod::class)],
            'payment_date' => ['required', 'date'],
            'remarks' => ['nullable', 'string', 'max:500'],
            'official_receipt_number' => [
                'required', 'string', 'max:50',
                Rule::unique('payments', 'official_receipt_number')->ignore($paymentId),
            ],
        ];
    }
}
