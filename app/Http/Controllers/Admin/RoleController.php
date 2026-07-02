<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AppPermission;
use App\Enums\AppRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateRolePermissionsRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Display roles and their permissions.
     */
    public function index(Request $request): Response
    {
        $roles = Role::query()
            ->where('guard_name', 'web')
            ->with('permissions')
            ->orderBy('name')
            ->get()
            ->map(function (Role $role) {
                $appRole = AppRole::tryFrom($role->name);
                $permissionValues = $role->permissions->pluck('name')->all();

                return [
                    'value' => $role->name,
                    'label' => $appRole?->label() ?? ucfirst($role->name),
                    'permissions' => collect(AppPermission::cases())
                        ->map(fn (AppPermission $permission) => [
                            'value' => $permission->value,
                            'label' => $permission->label(),
                            'assigned' => in_array($permission->value, $permissionValues, true),
                        ])
                        ->values()
                        ->all(),
                ];
            });

        return Inertia::render('admin/roles/index', [
            'roles' => $roles,
        ]);
    }

    /**
     * Update the role's permissions.
     */
    public function update(UpdateRolePermissionsRequest $request, string $role): RedirectResponse
    {
        $roleModel = Role::findByName($role, 'web');
        $roleModel->syncPermissions($request->validated('permissions'));

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Role permissions updated.')]);

        return to_route('admin.roles.index');
    }
}
