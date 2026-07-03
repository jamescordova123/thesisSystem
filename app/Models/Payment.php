<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id', 'student_fee_assessment_id', 'official_receipt_number',
    'amount', 'payment_method', 'payment_date', 'cashier_id', 'remarks',
])]
class Payment extends Model
{
    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsTo<StudentFeeAssessment, $this> */
    public function assessment(): BelongsTo
    {
        return $this->belongsTo(StudentFeeAssessment::class, 'student_fee_assessment_id');
    }

    /** @return BelongsTo<User, $this> */
    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'payment_date' => 'datetime',
        ];
    }
}
