<?php

namespace Database\Seeders;

use App\Enums\AppPermission;
use App\Enums\AppRole;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Seed application roles and permissions.
     */
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        foreach (AppPermission::cases() as $permission) {
            Permission::findOrCreate($permission->value, 'web');
        }

        $superAdmin = Role::findOrCreate(AppRole::SuperAdmin->value, 'web');
        $superAdmin->syncPermissions(Permission::all());

        Role::findOrCreate(AppRole::Admin->value, 'web')
            ->syncPermissions([
                AppPermission::ManageUsers->value,
                AppPermission::ManageTheses->value,
                AppPermission::ViewTheses->value,
                AppPermission::ReviewThesis->value,
            ]);

        Role::findOrCreate(AppRole::Student->value, 'web')
            ->syncPermissions([
                AppPermission::ViewTheses->value,
                AppPermission::SubmitThesis->value,
            ]);

        Role::findOrCreate(AppRole::Advisor->value, 'web')
            ->syncPermissions([
                AppPermission::ViewTheses->value,
                AppPermission::ReviewThesis->value,
            ]);
    }
}
