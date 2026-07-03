<?php

namespace App\Services;

use App\Enums\PaymentStatus;
use App\Models\StudentFeeAssessment;
use App\Models\User;

class CashierStudentPresenter
{
    /**
     * @return array<string, mixed>
     */
    public static function studentSummary(User $user, ?StudentFeeAssessment $assessment = null): array
    {
        $user->loadMissing(['studentInformation.program', 'studentInformation.section']);
        $info = $user->studentInformation;
        $assessment ??= FeeAssessmentService::getOrCreateForUser($user);
        $assessment->loadMissing('payments');
        $status = $assessment->paymentStatus();

        return [
            'id' => $user->id,
            'student_number' => $info?->student_number,
            'full_name' => $info?->full_name ?? $user->name,
            'email' => $user->email,
            'program' => $info?->program?->name ?? $info?->academic_program,
            'program_id' => $info?->program_id,
            'year_level' => $info?->year_level,
            'section' => $info?->section?->name,
            'section_id' => $info?->section_id,
            'assessment_id' => $assessment->id,
            'fees' => $assessment->feeBreakdown(),
            'total_assessment' => $assessment->totalAssessment(),
            'total_payments' => $assessment->totalPayments(),
            'remaining_balance' => $assessment->remainingBalance(),
            'payment_status' => $status->value,
            'payment_status_label' => $status->label(),
            'payment_status_color' => $status->color(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function ledger(User $user, StudentFeeAssessment $assessment): array
    {
        $assessment->load(['academicYear', 'semester', 'payments.cashier']);
        $summary = self::studentSummary($user, $assessment);

        return [
            ...$summary,
            'academic_year' => $assessment->academicYear->label,
            'semester' => $assessment->semester->name,
            'payments' => $assessment->payments
                ->sortByDesc('payment_date')
                ->values()
                ->map(fn ($payment) => [
                    'id' => $payment->id,
                    'payment_date' => $payment->payment_date?->toIso8601String(),
                    'official_receipt_number' => $payment->official_receipt_number,
                    'amount' => $payment->amount,
                    'payment_method' => $payment->payment_method,
                    'cashier' => $payment->cashier?->name,
                    'remarks' => $payment->remarks,
                ]),
        ];
    }
}
