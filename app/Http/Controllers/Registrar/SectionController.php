<?php

namespace App\Http\Controllers\Registrar;

use App\Enums\AppRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Registrar\StoreSectionRequest;
use App\Http\Requests\Registrar\UpdateSectionRequest;
use App\Models\AcademicYear;
use App\Models\Program;
use App\Models\Section;
use App\Models\Semester;
use App\Models\StudentInformation;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SectionController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Section::query()
            ->with(['program', 'semester', 'academicYear', 'adviser', 'students.studentInformation'])
            ->withCount('students')
            ->where('is_archived', $request->boolean('archived'));

        if ($request->filled('program_id')) {
            $query->where('program_id', $request->integer('program_id'));
        }

        if ($request->filled('year_level')) {
            $query->where('year_level', $request->integer('year_level'));
        }

        if ($search = $request->string('search')->trim()->toString()) {
            $query->where('name', 'like', "%{$search}%");
        }

        $sections = $query->orderBy('name')->paginate(12)->withQueryString()
            ->through(fn (Section $section) => [
                'id' => $section->id,
                'name' => $section->name,
                'program' => $section->program->name,
                'program_id' => $section->program_id,
                'year_level' => $section->year_level,
                'semester' => $section->semester->name,
                'semester_id' => $section->semester_id,
                'academic_year' => $section->academicYear->label,
                'academic_year_id' => $section->academic_year_id,
                'adviser' => $section->adviser?->name,
                'adviser_id' => $section->adviser_id,
                'max_capacity' => $section->max_capacity,
                'students_count' => $section->students_count,
                'remaining_slots' => max(0, $section->max_capacity - $section->students_count),
                'is_archived' => $section->is_archived,
                'students' => $section->students->map(fn (User $u) => [
                    'id' => $u->id,
                    'name' => $u->studentInformation?->full_name ?? $u->name,
                    'student_number' => $u->studentInformation?->student_number,
                ]),
            ]);

        return Inertia::render('registrar/sections/index', [
            'sections' => $sections,
            'filters' => $request->only(['search', 'program_id', 'year_level', 'archived']),
            'programs' => Program::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'semesters' => Semester::query()->where('is_active', true)->orderBy('sort_order')->get(['id', 'name']),
            'academicYears' => AcademicYear::query()->where('is_active', true)->orderByDesc('label')->get(['id', 'label']),
            'yearLevels' => collect(range(1, 4))->map(fn ($l) => ['value' => $l, 'label' => "Year {$l}"])->all(),
            'students' => User::havingAppRole(AppRole::Student)->with('studentInformation')
                ->whereHas('studentInformation', fn ($q) => $q->where('is_archived', false))
                ->orderBy('name')->get()
                ->map(fn (User $u) => [
                    'id' => $u->id,
                    'name' => $u->studentInformation?->full_name ?? $u->name,
                    'student_number' => $u->studentInformation?->student_number,
                ]),
        ]);
    }

    public function store(StoreSectionRequest $request): RedirectResponse
    {
        $section = Section::create($request->validated());
        AuditLogger::log('section.created', $section, null, $section->toArray());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Section created.')]);

        return to_route('registrar.sections.index');
    }

    public function update(UpdateSectionRequest $request, Section $section): RedirectResponse
    {
        $old = $section->toArray();
        $section->update($request->validated());
        AuditLogger::log('section.updated', $section, $old, $section->toArray());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Section updated.')]);

        return to_route('registrar.sections.index');
    }

    public function destroy(Section $section): RedirectResponse
    {
        $section->update(['is_archived' => true]);
        AuditLogger::log('section.archived', $section);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Section archived.')]);

        return to_route('registrar.sections.index');
    }

    public function assignStudent(Request $request, Section $section): RedirectResponse
    {
        $request->validate(['user_id' => ['required', 'integer', 'exists:users,id']]);

        if ($section->remainingSlots() <= 0) {
            return back()->withErrors(['user_id' => __('Section is at full capacity.')]);
        }

        $userId = $request->integer('user_id');

        if ($section->students()->where('user_id', $userId)->exists()) {
            return back()->withErrors(['user_id' => __('Student is already assigned to this section.')]);
        }

        $section->students()->attach($userId, ['assigned_at' => now()]);
        StudentInformation::query()->where('user_id', $userId)->update(['section_id' => $section->id]);

        AuditLogger::log('section.student_assigned', $section, null, ['user_id' => $userId]);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Student assigned to section.')]);

        return back();
    }

    public function removeStudent(Section $section, User $user): RedirectResponse
    {
        $section->students()->detach($user->id);
        StudentInformation::query()->where('user_id', $user->id)->where('section_id', $section->id)->update(['section_id' => null]);

        AuditLogger::log('section.student_removed', $section, null, ['user_id' => $user->id]);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Student removed from section.')]);

        return back();
    }

    public function transferStudent(Request $request, Section $section): RedirectResponse
    {
        $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'target_section_id' => ['required', 'integer', 'exists:sections,id', 'different:section'],
        ]);

        $userId = $request->integer('user_id');
        $target = Section::findOrFail($request->integer('target_section_id'));

        if ($target->remainingSlots() <= 0) {
            return back()->withErrors(['target_section_id' => __('Target section is at full capacity.')]);
        }

        $section->students()->detach($userId);
        $target->students()->syncWithoutDetaching([$userId => ['assigned_at' => now()]]);
        StudentInformation::query()->where('user_id', $userId)->update(['section_id' => $target->id]);

        AuditLogger::log('section.student_transferred', $section, null, [
            'user_id' => $userId,
            'from_section_id' => $section->id,
            'to_section_id' => $target->id,
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Student transferred successfully.')]);

        return back();
    }
}
