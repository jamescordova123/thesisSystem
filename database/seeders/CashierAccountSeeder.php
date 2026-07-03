<?php

namespace Database\Seeders;

use App\Actions\Teams\CreateTeam;
use App\Enums\AppRole;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class CashierAccountSeeder extends Seeder
{
    public function run(CreateTeam $createTeam): void
    {
        $user = User::query()->firstOrCreate(
            ['email' => 'cashier2026@diltc.edu'],
            [
                'name' => 'Cashier',
                'password' => 'cashier@diltc',
                'role_id' => Role::idFor(AppRole::Cashier),
            ],
        );

        if (! $user->hasRole(AppRole::Cashier->value)) {
            $user->syncRoles([AppRole::Cashier->value]);
        }

        if ($user->teams()->count() === 0) {
            $createTeam->handle($user, $user->name."'s Team", isPersonal: true);
        }
    }
}
