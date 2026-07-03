<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\User;
use App\Services\CashierExportService;
use App\Services\CashierStudentPresenter;
use App\Services\FeeAssessmentService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    public function __construct(
        private CashierExportService $exporter,
    ) {}

    public function dailyCollection(Request $request): StreamedResponse|View
    {
        if ($request->query('format') === 'csv') {
            return $this->exporter->dailyCollectionCsv();
        }

        $payments = Payment::query()
            ->with(['user.studentInformation', 'cashier'])
            ->whereDate('payment_date', today())
            ->orderByDesc('payment_date')
            ->get();

        return view('cashier.exports.daily-collection', compact('payments'));
    }

    public function monthlyCollection(Request $request): StreamedResponse|View
    {
        if ($request->query('format') === 'csv') {
            return $this->exporter->monthlyCollectionCsv();
        }

        $payments = Payment::query()
            ->with(['user.studentInformation', 'cashier'])
            ->whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year)
            ->orderByDesc('payment_date')
            ->get();

        return view('cashier.exports.monthly-collection', compact('payments'));
    }

    public function outstandingBalance(Request $request): StreamedResponse
    {
        return $this->exporter->outstandingBalanceCsv();
    }

    public function fullyPaid(Request $request): StreamedResponse
    {
        return $this->exporter->fullyPaidCsv();
    }

    public function partialPayment(Request $request): StreamedResponse
    {
        return $this->exporter->partialPaymentCsv();
    }

    public function noPayment(Request $request): StreamedResponse
    {
        return $this->exporter->noPaymentCsv();
    }

    public function ledger(Request $request, User $user): StreamedResponse|View
    {
        $assessment = FeeAssessmentService::getOrCreateForUser($user);
        $ledger = CashierStudentPresenter::ledger($user, $assessment);

        if ($request->query('format') === 'csv') {
            return $this->exporter->ledgerCsv($user, $assessment);
        }

        return view('cashier.exports.ledger', compact('ledger'));
    }

    public function receipt(Payment $payment): View
    {
        $payment->load(['user.studentInformation', 'cashier', 'assessment.academicYear', 'assessment.semester']);

        return view('cashier.exports.receipt', compact('payment'));
    }
}
