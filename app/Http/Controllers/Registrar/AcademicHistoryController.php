<?php

namespace App\Http\Controllers\Registrar;

use App\Enums\AppRole;
use App\Http\Controllers\Controller;
use App\Models\AcademicHistory;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AcademicHistoryController extends Controller
{
    public function index(Request $request): Response
    {
        $students = collect();

        if ($search = $request->string('search')->trim()->toString()) {
            $students = User::havingAppRole(AppRole::Student)
                ->with('studentInformation')
                ->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhereHas('studentInformation', fn ($sq) => $sq
                            ->where('student_number', 'like', "%{$search}%")
                            ->orWhere('full_name', 'like', "%{$search}%"));
                })
                ->limit(30)
                ->get()
                ->map(fn (User $u) => [
                    'id' => $u->id,
                    'student_number' => $u->studentInformation?->student_number,
                    'name' => $u->studentInformation?->full_name ?? $u->name,
                    'program' => $u->studentInformation?->academic_program,
                ]);
        }

        return Inertia::render('registrar/academic-history/index', [
            'students' => $students,
            'search' => $search ?? '',
        ]);
    }

    public function show(User $user): Response
    {
        $user->load('studentInformation');

        $histories = AcademicHistory::query()
            ->where('user_id', $user->id)
            ->with(['academicYear', 'semester'])
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (AcademicHistory $h) => [
                'subject_code' => $h->subject_code,
                'subject_name' => $h->subject_name,
                'grade' => $h->grade,
                'gpa' => $h->gpa,
                'gwa' => $h->gwa,
                'academic_standing' => $h->academic_standing,
                'academic_year' => $h->academicYear?->label,
                'semester' => $h->semester?->name,
            ]);

        return Inertia::render('registrar/academic-history/show', [
            'student' => [
                'id' => $user->id,
                'student_number' => $user->studentInformation?->student_number,
                'name' => $user->studentInformation?->full_name ?? $user->name,
                'program' => $user->studentInformation?->academic_program,
            ],
            'histories' => $histories,
        ]);
    }
}
