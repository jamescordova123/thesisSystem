<?php

namespace Database\Seeders;

use App\Enums\AppPermission;
use App\Enums\AppRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use App\Models\Role;
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

        Role::findOrCreate(AppRole::Registrar->value, 'web')
            ->syncPermissions([
                AppPermission::ManageUsers->value,
                AppPermission::ManageTheses->value,
                AppPermission::ViewTheses->value,
                AppPermission::ReviewThesis->value,
            ]);

        Role::findOrCreate(AppRole::Cashier->value, 'web')
            ->syncPermissions([
                AppPermission::ViewTheses->value,
            ]);

        Role::findOrCreate(AppRole::Student->value, 'web')
            ->syncPermissions([
                AppPermission::ViewTheses->value,
                AppPermission::SubmitThesis->value,
            ]);

        $this->removeObsoleteRoles();
    }

    /**
     * Remove any web roles that are no longer defined in the AppRole enum.
     *
     * Detaches the role from users and permissions before deleting it so
     * no orphaned pivot records are left behind.
     */
    protected function removeObsoleteRoles(): void
    {
        $currentRoles = array_map(
            fn (AppRole $role) => $role->value,
            AppRole::cases()
        );

        Role::query()
            ->where('guard_name', 'web')
            ->whereNotIn('name', $currentRoles)
            ->get()
            ->each(function (Role $role): void {
                $role->syncPermissions([]);
                DB::table('model_has_roles')->where('role_id', $role->id)->delete();
                User::query()->where('role_id', $role->id)->update(['role_id' => null]);
                $role->delete();
            });
    }
}
