<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Teams\CreateTeam;
use App\Enums\AppRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRolesRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request): Response
    {
        return Inertia::render('admin/users/index', [
            'users' => User::query()
                ->with('roles')
                ->orderBy('name')
                ->get()
                ->map(fn (User $user) => $this->toUserPayload($user)),
            'availableRoles' => AppRole::options(),
        ]);
    }

    /**
     * Store a newly created user.
     */
    public function store(StoreUserRequest $request, CreateTeam $createTeam): RedirectResponse
    {
        DB::transaction(function () use ($request, $createTeam) {
            $user = User::create([
                'name' => $request->validated('name'),
                'email' => $request->validated('email'),
                'password' => $request->validated('password'),
            ]);

            $user->syncRoles($request->validated('roles'));

            $createTeam->handle($user, $user->name."'s Team", isPersonal: true);
        });

        Inertia::flash('toast', ['type' => 'success', 'message' => __('User created.')]);

        return to_route('admin.users.index');
    }

    /**
     * Show the user role edit page.
     */
    public function edit(User $user): Response
    {
        return Inertia::render('admin/users/edit', [
            'user' => $this->toUserPayload($user->load('roles')),
            'availableRoles' => AppRole::options(),
        ]);
    }

    /**
     * Update the user's roles.
     */
    public function update(UpdateUserRolesRequest $request, User $user): RedirectResponse
    {
        $user->syncRoles($request->validated('roles'));

        Inertia::flash('toast', ['type' => 'success', 'message' => __('User roles updated.')]);

        return to_route('admin.users.edit', ['user' => $user]);
    }

    /**
     * @return array<string, mixed>
     */
    private function toUserPayload(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'roles' => $user->roles->map(function ($role) {
                $appRole = AppRole::tryFrom($role->name);

                return [
                    'value' => $role->name,
                    'label' => $appRole?->label() ?? ucfirst($role->name),
                ];
            })->values()->all(),
        ];
    }
}
