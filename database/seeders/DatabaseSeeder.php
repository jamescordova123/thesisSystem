<?php

namespace Database\Seeders;

use App\Enums\AppRole;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleAndPermissionSeeder::class);
        $this->call(RegistrarAcademicSeeder::class);
        $this->call(RegistrarCurriculumSeeder::class);
        $this->call(RegistrarAccountSeeder::class);
        $this->call(CashierAccountSeeder::class);

        if (! User::query()->where('email', 'superadmin@example.com')->exists()) {
            User::factory()->create([
                'name' => 'Super Admin',
                'email' => 'superadmin@example.com',
                'role_id' => Role::idFor(AppRole::SuperAdmin),
            ]);
        }
    }
}
