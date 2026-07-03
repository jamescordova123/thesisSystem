<?php

namespace App\Services;

use App\Enums\AppRole;
use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Models\StudentFeeAssessment;
use App\Models\StudentInformation;
use App\Models\User;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CashierExportService
{
    /**
     * @param  array<int, string>  $headers
     * @param  iterable<int, array<int, string|int|float|null>>  $rows
     */
    public function csv(string $filename, array $headers, iterable $rows): StreamedResponse
    {
        return response()->streamDownload(function () use ($headers, $rows) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $headers);

            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    public function dailyCollectionCsv(): StreamedResponse
    {
        $payments = Payment::query()
            ->with(['user.studentInformation', 'cashier'])
            ->whereDate('payment_date', today())
            ->orderByDesc('payment_date')
            ->get();

        $rows = $payments->map(fn (Payment $p) => [
            $p->official_receipt_number,
            $p->user->studentInformation?->student_number ?? '',
            $p->user->studentInformation?->full_name ?? $p->user->name,
            $p->amount,
            $p->payment_method,
            $p->cashier?->name,
            $p->payment_date?->format('Y-m-d H:i'),
        ]);

        return $this->csv(
            'daily-collection-'.today()->format('Y-m-d').'.csv',
            ['OR Number', 'Student ID', 'Name', 'Amount', 'Method', 'Cashier', 'Date'],
            $rows,
        );
    }

    public function monthlyCollectionCsv(): StreamedResponse
    {
        $payments = Payment::query()
            ->with(['user.studentInformation', 'cashier'])
            ->whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year)
            ->orderByDesc('payment_date')
            ->get();

        $rows = $payments->map(fn (Payment $p) => [
            $p->official_receipt_number,
            $p->user->studentInformation?->student_number ?? '',
            $p->user->studentInformation?->full_name ?? $p->user->name,
            $p->amount,
            $p->payment_method,
            $p->cashier?->name,
            $p->payment_date?->format('Y-m-d H:i'),
        ]);

        return $this->csv(
            'monthly-collection-'.now()->format('Y-m').'.csv',
            ['OR Number', 'Student ID', 'Name', 'Amount', 'Method', 'Cashier', 'Date'],
            $rows,
        );
    }

    public function outstandingBalanceCsv(): StreamedResponse
    {
        $rows = $this->assessmentRows()
            ->filter(fn (array $row) => $row['status'] !== PaymentStatus::FullyPaid->value)
            ->map(fn (array $row) => [
                $row['student_number'],
                $row['name'],
                $row['program'],
                $row['total_assessment'],
                $row['total_payments'],
                $row['remaining_balance'],
                $row['status_label'],
            ]);

        return $this->csv(
            'outstanding-balance.csv',
            ['Student ID', 'Name', 'Program', 'Assessment', 'Paid', 'Balance', 'Status'],
            $rows,
        );
    }

    public function fullyPaidCsv(): StreamedResponse
    {
        return $this->statusReportCsv('fully-paid.csv', PaymentStatus::FullyPaid);
    }

    public function partialPaymentCsv(): StreamedResponse
    {
        return $this->statusReportCsv('partial-payment.csv', PaymentStatus::Partial);
    }

    public function noPaymentCsv(): StreamedResponse
    {
        return $this->statusReportCsv('no-payment.csv', PaymentStatus::NoPayment);
    }

    public function ledgerCsv(User $user, StudentFeeAssessment $assessment): StreamedResponse
    {
        $user->load('studentInformation.program', 'studentInformation.section');
        $assessment->load(['academicYear', 'semester', 'payments.cashier']);

        $rows = $assessment->payments->map(fn (Payment $p) => [
            $p->payment_date?->format('Y-m-d H:i'),
            $p->official_receipt_number,
            $p->amount,
            $p->payment_method,
            $p->cashier?->name,
            $p->remarks,
        ]);

        return $this->csv(
            'ledger-'.($user->studentInformation?->student_number ?? $user->id).'.csv',
            ['Date', 'OR Number', 'Amount', 'Method', 'Cashier', 'Remarks'],
            $rows,
        );
    }

    private function statusReportCsv(string $filename, PaymentStatus $status): StreamedResponse
    {
        $rows = $this->assessmentRows()
            ->filter(fn (array $row) => $row['status'] === $status->value)
            ->map(fn (array $row) => [
                $row['student_number'],
                $row['name'],
                $row['program'],
                $row['total_assessment'],
                $row['total_payments'],
                $row['remaining_balance'],
            ]);

        return $this->csv(
            $filename,
            ['Student ID', 'Name', 'Program', 'Assessment', 'Paid', 'Balance'],
            $rows,
        );
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function assessmentRows(): Collection
    {
        return StudentFeeAssessment::query()
            ->with(['user.studentInformation.program', 'payments'])
            ->get()
            ->map(function (StudentFeeAssessment $assessment) {
                $info = $assessment->user->studentInformation;
                $status = $assessment->paymentStatus();

                return [
                    'student_number' => $info?->student_number ?? '',
                    'name' => $info?->full_name ?? $assessment->user->name,
                    'program' => $info?->program?->name ?? $info?->academic_program ?? '',
                    'total_assessment' => $assessment->totalAssessment(),
                    'total_payments' => $assessment->totalPayments(),
                    'remaining_balance' => $assessment->remainingBalance(),
                    'status' => $status->value,
                    'status_label' => $status->label(),
                ];
            });
    }
}
