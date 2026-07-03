<?php

namespace App\Http\Controllers\Cashier;

use App\Enums\AppRole;
use App\Enums\PaymentMethod;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cashier\StorePaymentRequest;
use App\Http\Requests\Cashier\UpdatePaymentRequest;
use App\Models\Payment;
use App\Models\StudentFeeAssessment;
use App\Models\User;
use App\Services\AuditLogger;
use App\Services\CashierStudentPresenter;
use App\Services\FeeAssessmentService;
use App\Services\ReceiptNumberGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PaymentController extends Controller
{
    public function index(Request $request): Response
    {
        $query = User::havingAppRole(AppRole::Student)
            ->with(['studentInformation.program', 'studentInformation.section']);

        if ($search = $request->string('search')->trim()->toString()) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhereHas('studentInformation', fn ($sq) => $sq
                        ->where('student_number', 'like', "%{$search}%")
                        ->orWhere('full_name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('program_id')) {
            $query->whereHas('studentInformation', fn ($q) => $q->where('program_id', $request->integer('program_id')));
        }

        if ($request->filled('year_level')) {
            $query->whereHas('studentInformation', fn ($q) => $q->where('year_level', $request->integer('year_level')));
        }

        if ($request->filled('section_id')) {
            $query->whereHas('studentInformation', fn ($q) => $q->where('section_id', $request->integer('section_id')));
        }

        $students = $query->orderBy('name')->paginate(15)->withQueryString()
            ->through(function (User $user) {
                $assessment = FeeAssessmentService::getOrCreateForUser($user);

                return CashierStudentPresenter::studentSummary($user, $assessment);
            });

        return Inertia::render('cashier/payments/index', [
            'students' => $students,
            'filters' => $request->only(['search', 'program_id', 'year_level', 'section_id']),
            'programs' => \App\Models\Program::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'sections' => \App\Models\Section::query()->where('is_archived', false)->orderBy('name')->get(['id', 'name']),
            'yearLevels' => collect(range(1, 4))->map(fn ($l) => ['value' => $l, 'label' => "Year {$l}"])->all(),
        ]);
    }

    public function show(User $user): Response
    {
        $assessment = FeeAssessmentService::getOrCreateForUser($user);

        return Inertia::render('cashier/payments/show', [
            'student' => CashierStudentPresenter::ledger($user, $assessment),
            'paymentMethods' => PaymentMethod::options(),
        ]);
    }

    public function store(StorePaymentRequest $request, User $user): RedirectResponse
    {
        $assessment = StudentFeeAssessment::findOrFail($request->integer('student_fee_assessment_id'));

        abort_unless($assessment->user_id === $user->id, 403);

        if ($request->float('amount') > $assessment->remainingBalance()) {
            return back()->withErrors(['amount' => __('Payment amount exceeds remaining balance.')]);
        }

        $payment = Payment::create([
            'user_id' => $user->id,
            'student_fee_assessment_id' => $assessment->id,
            'official_receipt_number' => $request->validated('official_receipt_number')
                ?? ReceiptNumberGenerator::generate(),
            'amount' => $request->validated('amount'),
            'payment_method' => $request->validated('payment_method'),
            'payment_date' => $request->validated('payment_date'),
            'cashier_id' => $request->user()->id,
            'remarks' => $request->validated('remarks'),
        ]);

        AuditLogger::log('payment.created', $payment, null, $payment->toArray());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Payment recorded successfully.')]);

        return to_route('cashier.payments.show', $user);
    }

    public function update(UpdatePaymentRequest $request, User $user, Payment $payment): RedirectResponse
    {
        abort_unless($payment->user_id === $user->id, 403);

        $old = $payment->toArray();
        $assessment = $payment->assessment;
        $otherPayments = $assessment->totalPayments() - (float) $payment->amount;
        $newTotal = $otherPayments + $request->float('amount');

        if ($newTotal > $assessment->totalAssessment()) {
            return back()->withErrors(['amount' => __('Updated total payments would exceed assessment.')]);
        }

        $payment->update($request->validated());
        AuditLogger::log('payment.updated', $payment, $old, $payment->toArray());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Payment updated successfully.')]);

        return to_route('cashier.payments.show', $user);
    }
}
