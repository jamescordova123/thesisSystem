<?php

namespace App\Http\Controllers\Registrar;

use App\Enums\AppRole;
use App\Enums\EnrollmentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Registrar\StoreEnrollmentRequest;
use App\Models\AcademicYear;
use App\Models\Curriculum;
use App\Models\Enrollment;
use App\Models\Program;
use App\Models\Section;
use App\Models\Semester;
use App\Models\StudentInformation;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class EnrollmentController extends Controller
{
    public function index(Request $request): Response
    {
        return Inertia::render('registrar/enrollments/index', [
            'students' => User::havingAppRole(AppRole::Student)
                ->with('studentInformation')
                ->whereHas('studentInformation', fn ($q) => $q->where('is_archived', false))
                ->when($request->filled('search'), function ($q) use ($request) {
                    $search = $request->string('search')->toString();
                    $q->where(function ($query) use ($search) {
                        $query->where('name', 'like', "%{$search}%")
                            ->orWhereHas('studentInformation', fn ($sq) => $sq
                                ->where('student_number', 'like', "%{$search}%")
                                ->orWhere('full_name', 'like', "%{$search}%"));
                    });
                })
                ->orderBy('name')
                ->limit(50)
                ->get()
                ->map(fn (User $u) => [
                    'id' => $u->id,
                    'name' => $u->studentInformation?->full_name ?? $u->name,
                    'student_number' => $u->studentInformation?->student_number,
                    'enrollment_status' => $u->studentInformation?->enrollment_status,
                ]),
            'programs' => Program::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'sections' => Section::query()->where('is_archived', false)->with('program')->orderBy('name')->get()
                ->map(fn (Section $s) => [
                    'id' => $s->id,
                    'name' => $s->name,
                    'program_id' => $s->program_id,
                    'year_level' => $s->year_level,
                    'remaining_slots' => $s->remainingSlots(),
                ]),
            'academicYears' => AcademicYear::query()->where('is_active', true)->orderByDesc('label')->get(['id', 'label']),
            'semesters' => Semester::query()->where('is_active', true)->orderBy('sort_order')->get(['id', 'name']),
            'enrollmentStatuses' => EnrollmentStatus::options(),
            'yearLevels' => collect(range(1, 4))->map(fn ($l) => ['value' => $l, 'label' => "Year {$l}"])->all(),
            'filters' => $request->only('search'),
            'curriculumSubjects' => $this->curriculumSubjects($request),
        ]);
    }

    /**
     * @return array<int, array{id: int, code: string, name: string, units: int}>
     */
    private function curriculumSubjects(Request $request): array
    {
        if (! $request->filled(['program_id', 'year_level', 'semester_id'])) {
            return [];
        }

        $curriculum = Curriculum::query()
            ->where('program_id', $request->integer('program_id'))
            ->where('year_level', $request->integer('year_level'))
            ->where('semester_id', $request->integer('semester_id'))
            ->with('subjects')
            ->first();

        return $curriculum?->subjects
            ->map(fn ($subject) => [
                'id' => $subject->id,
                'code' => $subject->code,
                'name' => $subject->name,
                'units' => $subject->units,
            ])
            ->values()
            ->all() ?? [];
    }

    public function store(StoreEnrollmentRequest $request): RedirectResponse
    {
        $user = User::findOrFail($request->integer('user_id'));
        $section = $request->filled('section_id') ? Section::find($request->integer('section_id')) : null;

        if ($section && $section->remainingSlots() <= 0) {
            return back()->withErrors(['section_id' => __('Selected section is full.')]);
        }

        DB::transaction(function () use ($request, $user, $section) {
            $enrollment = Enrollment::create([
                'user_id' => $user->id,
                'academic_year_id' => $request->integer('academic_year_id'),
                'semester_id' => $request->integer('semester_id'),
                'program_id' => $request->integer('program_id'),
                'year_level' => $request->integer('year_level'),
                'section_id' => $section?->id,
                'status' => $request->validated('status'),
                'enrolled_at' => $request->validated('enrolled_at') ?? now(),
                'created_by' => $request->user()->id,
                'remarks' => $request->validated('remarks'),
            ]);

            $curriculum = Curriculum::query()
                ->where('program_id', $request->integer('program_id'))
                ->where('year_level', $request->integer('year_level'))
                ->where('semester_id', $request->integer('semester_id'))
                ->with('subjects')
                ->first();

            $subjectIds = $request->input('subject_ids', $curriculum?->subjects->pluck('id')->all() ?? []);
            $enrollment->subjects()->sync($subjectIds);

            $program = Program::find($request->integer('program_id'));

            StudentInformation::query()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'enrollment_status' => $request->validated('status'),
                    'program_id' => $program->id,
                    'academic_program' => $program->name,
                    'section_id' => $section?->id,
                    'year_level' => $request->integer('year_level'),
                    'school_year' => AcademicYear::find($request->integer('academic_year_id'))?->label,
                ],
            );

            if ($section) {
                $section->students()->syncWithoutDetaching([$user->id => ['assigned_at' => now()]]);
            }

            AuditLogger::log('enrollment.created', $enrollment, null, $enrollment->toArray());
        });

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Enrollment saved successfully.')]);

        return to_route('registrar.enrollments.index');
    }
}
