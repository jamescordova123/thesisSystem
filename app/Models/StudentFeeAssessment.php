<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'user_id', 'academic_year_id', 'semester_id',
    'tuition', 'test_paper_fee', 'pta_fee', 'uniform_fee', 'certificates_fee', 'graduation_fee',
])]
class StudentFeeAssessment extends Model
{
    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsTo<AcademicYear, $this> */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /** @return BelongsTo<Semester, $this> */
    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    /** @return HasMany<Payment, $this> */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function totalAssessment(): float
    {
        return (float) (
            $this->tuition
            + $this->test_paper_fee
            + $this->pta_fee
            + $this->uniform_fee
            + $this->certificates_fee
            + $this->graduation_fee
        );
    }

    public function totalPayments(): float
    {
        return (float) $this->payments()->sum('amount');
    }

    public function remainingBalance(): float
    {
        return max(0, $this->totalAssessment() - $this->totalPayments());
    }

    public function paymentStatus(): PaymentStatus
    {
        $paid = $this->totalPayments();

        if ($paid <= 0) {
            return PaymentStatus::NoPayment;
        }

        if ($paid >= $this->totalAssessment()) {
            return PaymentStatus::FullyPaid;
        }

        return PaymentStatus::Partial;
    }

    /**
     * @return array<string, float>
     */
    public function feeBreakdown(): array
    {
        return [
            'tuition' => (float) $this->tuition,
            'test_paper_fee' => (float) $this->test_paper_fee,
            'pta_fee' => (float) $this->pta_fee,
            'uniform_fee' => (float) $this->uniform_fee,
            'certificates_fee' => (float) $this->certificates_fee,
            'graduation_fee' => (float) $this->graduation_fee,
        ];
    }

    protected function casts(): array
    {
        return [
            'tuition' => 'decimal:2',
            'test_paper_fee' => 'decimal:2',
            'pta_fee' => 'decimal:2',
            'uniform_fee' => 'decimal:2',
            'certificates_fee' => 'decimal:2',
            'graduation_fee' => 'decimal:2',
        ];
    }
}
