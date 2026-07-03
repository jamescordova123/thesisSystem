<?php

namespace App\Http\Controllers\Cashier;

use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\StudentFeeAssessment;
use Inertia\Inertia;
use Inertia\Response;

class ReportController extends Controller
{
    public function index(): Response
    {
        $assessments = StudentFeeAssessment::query()->with('payments')->get();

        return Inertia::render('cashier/reports/index', [
            'reportTypes' => [
                ['key' => 'daily-collection', 'label' => 'Daily Collection Report', 'count' => Payment::whereDate('payment_date', today())->count()],
                ['key' => 'monthly-collection', 'label' => 'Monthly Collection Report', 'count' => Payment::whereMonth('payment_date', now()->month)->whereYear('payment_date', now()->year)->count()],
                ['key' => 'student-payment', 'label' => 'Student Payment Report', 'count' => Payment::count()],
                ['key' => 'outstanding-balance', 'label' => 'Outstanding Balance Report', 'count' => $assessments->filter(fn ($a) => $a->paymentStatus() !== PaymentStatus::FullyPaid)->count()],
                ['key' => 'fully-paid', 'label' => 'Fully Paid Students Report', 'count' => $assessments->filter(fn ($a) => $a->paymentStatus() === PaymentStatus::FullyPaid)->count()],
                ['key' => 'partial-payment', 'label' => 'Partial Payment Report', 'count' => $assessments->filter(fn ($a) => $a->paymentStatus() === PaymentStatus::Partial)->count()],
                ['key' => 'no-payment', 'label' => 'No Payment Report', 'count' => $assessments->filter(fn ($a) => $a->paymentStatus() === PaymentStatus::NoPayment)->count()],
            ],
        ]);
    }
}
