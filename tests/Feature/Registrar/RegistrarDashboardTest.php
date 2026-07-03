<?php

namespace Tests\Feature\Registrar;

use App\Enums\AppRole;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RegistrarAcademicSeeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrarDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_registrar_can_access_dashboard(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);
        $this->seed(RegistrarAcademicSeeder::class);

        $registrar = User::factory()->create([
            'role_id' => Role::idFor(AppRole::Registrar),
        ]);

        $response = $this->actingAs($registrar)->get(route('registrar.dashboard'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page->component('registrar/dashboard'));
    }

    public function test_student_cannot_access_registrar_dashboard(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);

        $student = User::factory()->create([
            'role_id' => Role::idFor(AppRole::Student),
        ]);

        $response = $this->actingAs($student)->get(route('registrar.dashboard'));

        $response->assertForbidden();
    }
}
