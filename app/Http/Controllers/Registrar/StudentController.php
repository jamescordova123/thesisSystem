<?php

namespace App\Http\Controllers\Registrar;

use App\Enums\AppRole;
use App\Enums\EnrollmentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Registrar\StoreStudentRequest;
use App\Http\Requests\Registrar\UpdateStudentRequest;
use App\Models\AcademicYear;
use App\Models\Program;
use App\Models\Section;
use App\Models\Semester;
use App\Models\StudentInformation;
use App\Models\User;
use App\Services\AuditLogger;
use App\Services\StudentNumberGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

class StudentController extends Controller
{
    public function index(Request $request): Response
    {
        $query = User::havingAppRole(AppRole::Student)
            ->with(['studentInformation.program', 'studentInformation.section']);

        if ($search = $request->string('search')->trim()->toString()) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhereHas('studentInformation', function ($sq) use ($search) {
                        $sq->where('student_number', 'like', "%{$search}%")
                            ->orWhere('full_name', 'like', "%{$search}%");
                    });
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

        if ($request->filled('enrollment_status')) {
            $query->whereHas('studentInformation', fn ($q) => $q->where('enrollment_status', $request->string('enrollment_status')));
        }

        if ($request->boolean('archived')) {
            $query->whereHas('studentInformation', fn ($q) => $q->where('is_archived', true));
        } else {
            $query->where(function ($q) {
                $q->whereDoesntHave('studentInformation')
                    ->orWhereHas('studentInformation', fn ($sq) => $sq->where('is_archived', false));
            });
        }

        $students = $query->orderBy('name')->paginate(15)->withQueryString()
            ->through(fn (User $user) => $this->toPayload($user));

        return Inertia::render('registrar/students/index', [
            'students' => $students,
            'filters' => $request->only(['search', 'program_id', 'year_level', 'section_id', 'enrollment_status', 'archived']),
            ...$this->lookupData(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('registrar/students/create', $this->lookupData());
    }

    public function store(StoreStudentRequest $request): RedirectResponse
    {
        $studentRole = Role::findByName(AppRole::Student->value, 'web');

        $user = User::create([
            'name' => $request->validated('full_name'),
            'email' => $request->validated('email'),
            'password' => $request->validated('password'),
            'role_id' => $studentRole->id,
        ]);

        $program = Program::findOrFail($request->integer('program_id'));

        $info = StudentInformation::create([
            'user_id' => $user->id,
            'student_number' => StudentNumberGenerator::generate(),
            'full_name' => $request->validated('full_name'),
            'school_year' => $request->validated('school_year'),
            'academic_program' => $program->name,
            'program_id' => $program->id,
            'section_id' => $request->validated('section_id'),
            'year_level' => $request->validated('year_level'),
            'enrollment_status' => $request->validated('enrollment_status'),
            'status' => $request->validated('status'),
            'birthdate' => $request->validated('birthdate'),
            'sex' => $request->validated('sex'),
            'age' => $request->validated('age'),
            'place_of_birth' => $request->validated('place_of_birth'),
            'current_address' => $request->validated('current_address'),
            'permanent_address' => $request->validated('permanent_address'),
            'guardian_name' => $request->validated('guardian_name'),
            'primary_contact_number' => $request->validated('primary_contact_number'),
        ]);

        if ($info->section_id) {
            $info->section?->students()->syncWithoutDetaching([$user->id => ['assigned_at' => now()]]);
        }

        AuditLogger::log('student.created', $info, null, $info->toArray());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Student profile created.')]);

        return to_route('registrar.students.index');
    }

    public function edit(User $user): Response
    {
        $user->load(['studentInformation.program', 'studentInformation.section']);

        return Inertia::render('registrar/students/edit', [
            'student' => $this->toPayload($user),
            ...$this->lookupData(),
        ]);
    }

    public function update(UpdateStudentRequest $request, User $user): RedirectResponse
    {
        $info = $user->studentInformation;
        $old = $info?->toArray();

        $user->update([
            'name' => $request->validated('full_name'),
            'email' => $request->validated('email'),
        ]);

        $program = Program::findOrFail($request->integer('program_id'));

        $user->studentInformation()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'full_name' => $request->validated('full_name'),
                'school_year' => $request->validated('school_year'),
                'academic_program' => $program->name,
                'program_id' => $program->id,
                'section_id' => $request->validated('section_id'),
                'year_level' => $request->validated('year_level'),
                'enrollment_status' => $request->validated('enrollment_status'),
                'status' => $request->validated('status'),
                'birthdate' => $request->validated('birthdate'),
                'sex' => $request->validated('sex'),
                'age' => $request->validated('age'),
                'place_of_birth' => $request->validated('place_of_birth'),
                'current_address' => $request->validated('current_address'),
                'permanent_address' => $request->validated('permanent_address'),
                'guardian_name' => $request->validated('guardian_name'),
                'primary_contact_number' => $request->validated('primary_contact_number'),
                'father_name' => $request->validated('father_name'),
                'mother_maiden_name' => $request->validated('mother_maiden_name'),
            ],
        );

        $user->refresh()->load('studentInformation');
        AuditLogger::log('student.updated', $user->studentInformation, $old, $user->studentInformation->toArray());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Student profile updated.')]);

        return to_route('registrar.students.edit', $user);
    }

    public function archive(User $user): RedirectResponse
    {
        $info = $user->studentInformation;
        $info?->update(['is_archived' => true]);

        AuditLogger::log('student.archived', $info);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Student profile archived.')]);

        return to_route('registrar.students.index');
    }

    /**
     * @return array<string, mixed>
     */
    private function toPayload(User $user): array
    {
        $info = $user->studentInformation;

        return [
            'id' => $user->id,
            'email' => $user->email,
            'student_number' => $info?->student_number,
            'full_name' => $info?->full_name ?? $user->name,
            'school_year' => $info?->school_year,
            'program' => $info?->program?->name ?? $info?->academic_program,
            'program_id' => $info?->program_id,
            'section' => $info?->section?->name,
            'section_id' => $info?->section_id,
            'year_level' => $info?->year_level,
            'enrollment_status' => $info?->enrollment_status ?? EnrollmentStatus::NotEnrolled->value,
            'status' => $info?->status,
            'birthdate' => $info?->birthdate?->format('Y-m-d'),
            'sex' => $info?->sex,
            'age' => $info?->age,
            'place_of_birth' => $info?->place_of_birth,
            'current_address' => $info?->current_address,
            'permanent_address' => $info?->permanent_address,
            'guardian_name' => $info?->guardian_name,
            'primary_contact_number' => $info?->primary_contact_number,
            'father_name' => $info?->father_name,
            'mother_maiden_name' => $info?->mother_maiden_name,
            'is_archived' => $info?->is_archived ?? false,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function lookupData(): array
    {
        return [
            'programs' => Program::query()->where('is_active', true)->orderBy('name')->get(['id', 'code', 'name']),
            'sections' => Section::query()->where('is_archived', false)->with('program')->orderBy('name')->get()
                ->map(fn (Section $s) => [
                    'id' => $s->id,
                    'name' => $s->name,
                    'program' => $s->program->name,
                    'year_level' => $s->year_level,
                    'remaining_slots' => $s->remainingSlots(),
                ]),
            'academicYears' => AcademicYear::query()->where('is_active', true)->orderByDesc('label')->get(['id', 'label']),
            'semesters' => Semester::query()->where('is_active', true)->orderBy('sort_order')->get(['id', 'name']),
            'enrollmentStatuses' => EnrollmentStatus::options(),
            'yearLevels' => collect(range(1, 4))->map(fn ($l) => ['value' => $l, 'label' => "Year {$l}"])->all(),
        ];
    }
}
