<?php

namespace Database\Seeders;

use App\Actions\Teams\CreateTeam;
use App\Enums\AppRole;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class RegistrarAccountSeeder extends Seeder
{
    public function run(CreateTeam $createTeam): void
    {
        if (User::query()->where('email', 'registrar2026@diltc.edu')->exists()) {
            return;
        }

        $user = User::create([
            'name' => 'Registrar',
            'email' => 'registrar2026@diltc.edu',
            'password' => 'registrar@diltc',
            'role_id' => Role::idFor(AppRole::Registrar),
        ]);

        $createTeam->handle($user, $user->name."'s Team", isPersonal: true);
    }
}
