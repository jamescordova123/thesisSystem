<?php

namespace Tests\Feature\Registrar;

use App\Enums\AppRole;
use App\Models\AcademicYear;
use App\Models\Program;
use App\Models\Role;
use App\Models\Section;
use App\Models\Semester;
use App\Models\StudentInformation;
use App\Models\Subject;
use App\Models\User;
use Database\Seeders\RegistrarAcademicSeeder;
use Database\Seeders\RegistrarCurriculumSeeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RegistrarExportTest extends TestCase
{
    use RefreshDatabase;

    private User $registrar;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);
        $this->seed(RegistrarAcademicSeeder::class);
        $this->seed(RegistrarCurriculumSeeder::class);

        $this->registrar = User::factory()->create([
            'role_id' => Role::idFor(AppRole::Registrar),
        ]);
    }

    public function test_registrar_can_export_student_master_csv(): void
    {
        $studentRole = Role::findByName(AppRole::Student->value, 'web');
        User::factory()->create([
            'role_id' => $studentRole->id,
            'name' => 'Jane Student',
        ]);

        $response = $this->actingAs($this->registrar)
            ->get(route('registrar.exports.student-master', ['format' => 'csv']));

        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }

    public function test_registrar_can_export_class_list_print_view(): void
    {
        $program = Program::query()->where('code', 'BSIT')->firstOrFail();
        $semester = Semester::query()->where('name', '1st Semester')->firstOrFail();
        $academicYear = AcademicYear::query()->firstOrFail();

        $section = Section::create([
            'name' => 'BSIT-1A',
            'program_id' => $program->id,
            'year_level' => 1,
            'semester_id' => $semester->id,
            'academic_year_id' => $academicYear->id,
            'max_capacity' => 40,
        ]);

        $response = $this->actingAs($this->registrar)
            ->get(route('registrar.exports.class-list', $section));

        $response->assertOk();
        $response->assertSee('Official Class List');
    }

    public function test_registrar_can_upload_student_record(): void
    {
        Storage::fake('local');

        $studentRole = Role::findByName(AppRole::Student->value, 'web');
        $student = User::factory()->create(['role_id' => $studentRole->id]);

        StudentInformation::create([
            'user_id' => $student->id,
            'full_name' => $student->name,
            'school_year' => '2025-2026',
            'academic_program' => 'BSIT',
            'status' => 'regular',
            'birthdate' => '2000-01-01',
            'sex' => 'male',
            'age' => 20,
            'place_of_birth' => 'Manila',
            'current_address' => 'Manila',
            'permanent_address' => 'Manila',
            'guardian_name' => 'Guardian',
            'primary_contact_number' => '09123456789',
        ]);

        $response = $this->actingAs($this->registrar)->post(route('registrar.records.store', $student), [
            'title' => 'Birth Certificate',
            'record_type' => 'enrollment_requirement',
            'notes' => 'Submitted during enrollment',
            'file' => UploadedFile::fake()->create('birth-cert.pdf', 100, 'application/pdf'),
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('student_records', [
            'user_id' => $student->id,
            'title' => 'Birth Certificate',
            'record_type' => 'enrollment_requirement',
        ]);
    }

    public function test_curriculum_seeder_creates_subjects_for_bsit_year_one(): void
    {
        $this->assertGreaterThan(0, Subject::query()->count());

        $program = Program::query()->where('code', 'BSIT')->firstOrFail();
        $semester = Semester::query()->where('name', '1st Semester')->firstOrFail();

        $response = $this->actingAs($this->registrar)->get(route('registrar.enrollments.index', [
            'program_id' => $program->id,
            'year_level' => 1,
            'semester_id' => $semester->id,
        ]));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('registrar/enrollments/index')
            ->has('curriculumSubjects', 5));
    }
}
