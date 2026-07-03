<?php

namespace App\Http\Controllers\Registrar;

use App\Http\Controllers\Controller;
use App\Models\Section;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ClassListController extends Controller
{
    public function index(Request $request): Response
    {
        $sections = Section::query()
            ->with(['program', 'semester', 'academicYear'])
            ->withCount('students')
            ->when($request->filled('academic_year_id'), fn ($q) => $q->where('academic_year_id', $request->integer('academic_year_id')))
            ->when($request->filled('semester_id'), fn ($q) => $q->where('semester_id', $request->integer('semester_id')))
            ->when($request->filled('program_id'), fn ($q) => $q->where('program_id', $request->integer('program_id')))
            ->when($request->filled('year_level'), fn ($q) => $q->where('year_level', $request->integer('year_level')))
            ->where('is_archived', false)
            ->orderBy('name')
            ->get()
            ->map(fn (Section $s) => [
                'id' => $s->id,
                'name' => $s->name,
                'program' => $s->program->name,
                'year_level' => $s->year_level,
                'semester' => $s->semester->name,
                'academic_year' => $s->academicYear->label,
                'students_count' => $s->students_count,
            ]);

        return Inertia::render('registrar/class-lists/index', [
            'sections' => $sections,
            'filters' => $request->only(['academic_year_id', 'semester_id', 'program_id', 'year_level']),
        ]);
    }

    public function show(Section $section): Response
    {
        $section->load(['program', 'semester', 'academicYear', 'students.studentInformation']);

        return Inertia::render('registrar/class-lists/show', [
            'section' => [
                'id' => $section->id,
                'name' => $section->name,
                'program' => $section->program->name,
                'year_level' => $section->year_level,
                'semester' => $section->semester->name,
                'academic_year' => $section->academicYear->label,
            ],
            'students' => $section->students->map(fn ($u) => [
                'student_number' => $u->studentInformation?->student_number,
                'name' => $u->studentInformation?->full_name ?? $u->name,
                'enrollment_status' => $u->studentInformation?->enrollment_status,
            ]),
        ]);
    }
}
