<?php

namespace App\Http\Controllers\Cashier;

use App\Enums\AppRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\CashierStudentPresenter;
use App\Services\FeeAssessmentService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SearchController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $query = User::havingAppRole(AppRole::Student)
            ->with(['studentInformation.program', 'studentInformation.section']);

        if ($search = $request->string('q')->trim()->toString()) {
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

        $results = $query->orderBy('name')->limit(20)->get()
            ->map(function (User $user) {
                $assessment = FeeAssessmentService::getOrCreateForUser($user);

                return CashierStudentPresenter::studentSummary($user, $assessment);
            });

        return Inertia::render('cashier/search', [
            'results' => $results,
            'filters' => $request->only(['q', 'program_id', 'year_level', 'section_id']),
            'programs' => \App\Models\Program::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'sections' => \App\Models\Section::query()->where('is_archived', false)->orderBy('name')->get(['id', 'name']),
            'yearLevels' => collect(range(1, 4))->map(fn ($l) => ['value' => $l, 'label' => "Year {$l}"])->all(),
        ]);
    }
}
