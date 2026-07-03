<?php

namespace App\Http\Controllers\Registrar;

use App\Enums\AppRole;
use App\Http\Controllers\Controller;
use App\Models\AcademicHistory;
use App\Models\Section;
use App\Models\StudentInformation;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SearchController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $query = $request->string('q')->trim()->toString();

        $students = collect();
        $sections = collect();

        if (strlen($query) >= 2) {
            $students = User::havingAppRole(AppRole::Student)
                ->with(['studentInformation.program', 'studentInformation.section'])
                ->where(function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                        ->orWhereHas('studentInformation', fn ($sq) => $sq
                            ->where('student_number', 'like', "%{$query}%")
                            ->orWhere('full_name', 'like', "%{$query}%")
                            ->orWhere('academic_program', 'like', "%{$query}%"));
                })
                ->limit(20)
                ->get()
                ->map(fn (User $u) => [
                    'id' => $u->id,
                    'student_number' => $u->studentInformation?->student_number,
                    'name' => $u->studentInformation?->full_name ?? $u->name,
                    'program' => $u->studentInformation?->program?->name,
                    'year_level' => $u->studentInformation?->year_level,
                    'section' => $u->studentInformation?->section?->name,
                    'enrollment_status' => $u->studentInformation?->enrollment_status,
                ]);

            $sections = Section::query()
                ->with(['program', 'semester', 'academicYear'])
                ->where('name', 'like', "%{$query}%")
                ->orWhereHas('program', fn ($q) => $q->where('name', 'like', "%{$query}%"))
                ->limit(10)
                ->get()
                ->map(fn (Section $s) => [
                    'id' => $s->id,
                    'name' => $s->name,
                    'program' => $s->program->name,
                    'year_level' => $s->year_level,
                    'semester' => $s->semester->name,
                    'academic_year' => $s->academicYear->label,
                ]);
        }

        return Inertia::render('registrar/search', [
            'query' => $query,
            'students' => $students,
            'sections' => $sections,
        ]);
    }
}
