<?php

namespace Tests\Feature\Cashier;

use App\Enums\AppRole;
use App\Models\AcademicYear;
use App\Models\Role;
use App\Models\Semester;
use App\Models\StudentFeeAssessment;
use App\Models\StudentInformation;
use App\Models\StudentNotification;
use App\Models\User;
use Database\Seeders\RegistrarAcademicSeeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CashierDashboardTest extends TestCase
{
    use RefreshDatabase;

    private User $cashier;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);
        $this->seed(RegistrarAcademicSeeder::class);

        $this->cashier = User::factory()->create([
            'role_id' => Role::idFor(AppRole::Cashier),
        ]);
    }

    public function test_cashier_can_access_dashboard(): void
    {
        $response = $this->actingAs($this->cashier)->get(route('cashier.dashboard'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page->component('cashier/dashboard'));
    }

    public function test_student_cannot_access_cashier_dashboard(): void
    {
        $student = User::factory()->create([
            'role_id' => Role::idFor(AppRole::Student),
        ]);

        $response = $this->actingAs($student)->get(route('cashier.dashboard'));

        $response->assertForbidden();
    }

    public function test_cashier_can_record_payment(): void
    {
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

        $assessment = StudentFeeAssessment::create([
            'user_id' => $student->id,
            'academic_year_id' => AcademicYear::first()->id,
            'semester_id' => Semester::first()->id,
            'tuition' => 15000,
            'test_paper_fee' => 500,
            'pta_fee' => 300,
            'uniform_fee' => 1200,
            'certificates_fee' => 400,
            'graduation_fee' => 0,
        ]);

        $response = $this->actingAs($this->cashier)->post(route('cashier.payments.store', $student), [
            'student_fee_assessment_id' => $assessment->id,
            'amount' => 5000,
            'payment_method' => 'cash',
            'payment_date' => now()->toDateTimeString(),
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('payments', [
            'user_id' => $student->id,
            'amount' => 5000,
        ]);
    }

    public function test_cashier_can_send_notification_to_student(): void
    {
        $studentRole = Role::findByName(AppRole::Student->value, 'web');
        $student = User::factory()->create(['role_id' => $studentRole->id]);

        $response = $this->actingAs($this->cashier)->post(route('cashier.notifications.store'), [
            'user_id' => $student->id,
            'type' => 'payment_reminder',
            'subject' => 'Payment Due',
            'message' => 'Please settle your outstanding balance.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('student_notifications', [
            'user_id' => $student->id,
            'subject' => 'Payment Due',
        ]);
    }

    public function test_student_can_view_notifications(): void
    {
        $studentRole = Role::findByName(AppRole::Student->value, 'web');
        $student = User::factory()->create(['role_id' => $studentRole->id]);

        StudentNotification::create([
            'user_id' => $student->id,
            'sender_id' => $this->cashier->id,
            'type' => 'payment_reminder',
            'subject' => 'Reminder',
            'message' => 'Pay your fees.',
            'sent_at' => now(),
        ]);

        $response = $this->actingAs($student)->get(route('notifications.index'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('student/notifications/index')
            ->has('notifications.data', 1));
    }
}
