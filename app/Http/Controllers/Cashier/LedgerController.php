<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\CashierStudentPresenter;
use App\Services\FeeAssessmentService;
use Inertia\Inertia;
use Inertia\Response;

class LedgerController extends Controller
{
    public function show(User $user): Response
    {
        $assessment = FeeAssessmentService::getOrCreateForUser($user);

        return Inertia::render('cashier/ledger/show', [
            'ledger' => CashierStudentPresenter::ledger($user, $assessment),
        ]);
    }
}
