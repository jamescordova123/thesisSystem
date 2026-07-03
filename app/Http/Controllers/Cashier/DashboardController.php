<?php

namespace App\Http\Controllers\Cashier;

use App\Enums\AppRole;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\StudentFeeAssessment;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $todayTotal = Payment::query()->whereDate('payment_date', today())->sum('amount');
        $semesterTotal = Payment::query()
            ->whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year)
            ->sum('amount');

        $assessments = StudentFeeAssessment::query()->with('payments')->get();
        $outstanding = $assessments->filter(fn ($a) => $a->paymentStatus() !== PaymentStatus::FullyPaid)->count();
        $fullyPaid = $assessments->filter(fn ($a) => $a->paymentStatus() === PaymentStatus::FullyPaid)->count();

        $recentPayments = Payment::query()
            ->with(['user.studentInformation', 'cashier'])
            ->latest('payment_date')
            ->limit(8)
            ->get()
            ->map(fn (Payment $p) => [
                'id' => $p->id,
                'student' => $p->user->studentInformation?->full_name ?? $p->user->name,
                'student_number' => $p->user->studentInformation?->student_number,
                'amount' => $p->amount,
                'official_receipt_number' => $p->official_receipt_number,
                'payment_method' => $p->payment_method,
                'cashier' => $p->cashier?->name,
                'payment_date' => $p->payment_date?->toIso8601String(),
            ]);

        return Inertia::render('cashier/dashboard', [
            'stats' => [
                'todayTotal' => (float) $todayTotal,
                'semesterTotal' => (float) $semesterTotal,
                'outstandingStudents' => $outstanding,
                'fullyPaidStudents' => $fullyPaid,
            ],
            'recentPayments' => $recentPayments,
        ]);
    }
}
