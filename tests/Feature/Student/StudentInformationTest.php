<?php

namespace Tests\Feature\Student;

use App\Enums\AppRole;
use App\Enums\StudentSex;
use App\Enums\StudentStatus;
use App\Models\Role;
use App\Models\StudentInformation;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentInformationTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_view_information_sheet_form(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);

        $user = $this->createStudent();

        $response = $this->actingAs($user)->get(route('information-sheet.edit', [
            'current_team' => $user->currentTeam->slug,
        ]));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('student/information-sheet/index')
            ->where('information', null));
    }

    public function test_student_can_create_information_sheet(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);

        $user = $this->createStudent();

        $response = $this->actingAs($user)->post(route('information-sheet.store', [
            'current_team' => $user->currentTeam->slug,
        ]), $this->validPayload());

        $response->assertRedirect(route('information-sheet.edit', [
            'current_team' => $user->currentTeam->slug,
        ]));

        $this->assertDatabaseHas('student_information', [
            'user_id' => $user->id,
            'full_name' => 'Juan Dela Cruz',
            'school_year' => '2024-2025',
        ]);
    }

    public function test_student_can_update_existing_information_sheet(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);

        $user = $this->createStudent();

        StudentInformation::create([
            'user_id' => $user->id,
            ...$this->validPayload(),
        ]);

        $response = $this->actingAs($user)->post(route('information-sheet.store', [
            'current_team' => $user->currentTeam->slug,
        ]), [
            ...$this->validPayload(),
            'full_name' => 'Maria Santos',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('student_information', [
            'user_id' => $user->id,
            'full_name' => 'Maria Santos',
        ]);

        $this->assertDatabaseCount('student_information', 1);
    }

    public function test_non_student_cannot_access_information_sheet(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);

        $user = User::factory()->create([
            'role_id' => Role::idFor(AppRole::Registrar),
        ]);

        $response = $this->actingAs($user)->get(route('information-sheet.edit', [
            'current_team' => $user->currentTeam->slug,
        ]));

        $response->assertForbidden();
    }

    public function test_required_fields_are_validated(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);

        $user = $this->createStudent();

        $response = $this->actingAs($user)->post(route('information-sheet.store', [
            'current_team' => $user->currentTeam->slug,
        ]), []);

        $response->assertSessionHasErrors([
            'school_year',
            'full_name',
            'academic_program',
            'status',
            'birthdate',
            'sex',
            'age',
            'guardian_name',
            'primary_contact_number',
        ]);
    }

    private function createStudent(): User
    {
        return User::factory()->create([
            'role_id' => Role::idFor(AppRole::Student),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function validPayload(): array
    {
        return [
            'school_year' => '2024-2025',
            'full_name' => 'Juan Dela Cruz',
            'academic_program' => 'Bachelor of Science in Information Technology',
            'status' => StudentStatus::Regular->value,
            'birthdate' => '2002-05-15',
            'sex' => StudentSex::Male->value,
            'age' => 22,
            'place_of_birth' => 'Manila',
            'current_address' => '123 Main St, Quezon City',
            'permanent_address' => '456 Home St, Bulacan',
            'guardian_name' => 'Pedro Dela Cruz',
            'primary_contact_number' => '09171234567',
        ];
    }
}
