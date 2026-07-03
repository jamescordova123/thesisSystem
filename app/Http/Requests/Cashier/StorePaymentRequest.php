<?php

namespace App\Http\Requests\Cashier;

use App\Enums\AppPermission;
use App\Enums\PaymentMethod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePaymentRequest extends FormRequest
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
        return [
            'student_fee_assessment_id' => ['required', 'integer', 'exists:student_fee_assessments,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_method' => ['required', Rule::enum(PaymentMethod::class)],
            'payment_date' => ['required', 'date'],
            'remarks' => ['nullable', 'string', 'max:500'],
            'official_receipt_number' => ['nullable', 'string', 'max:50', 'unique:payments,official_receipt_number'],
        ];
    }
}
