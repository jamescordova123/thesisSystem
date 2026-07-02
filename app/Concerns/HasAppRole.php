<?php

namespace App\Concerns;

use App\Enums\AppRole;
use App\Models\Role;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasAppRole
{
    /**
     * Get the user's assigned role.
     *
     * @return BelongsTo<Role, $this>
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Assign an application role to the user.
     */
    public function assignAppRole(AppRole|string $role): void
    {
        $roleName = $role instanceof AppRole ? $role->value : $role;

        $this->role_id = Role::findByName($roleName, 'web')->id;
        $this->save();
    }

    /**
     * Bootstrap the trait.
     */
    protected static function bootHasAppRole(): void
    {
        static::saved(function (self $user): void {
            if (! $user->role_id) {
                return;
            }

            if (! $user->wasRecentlyCreated && ! $user->wasChanged('role_id')) {
                return;
            }

            $role = Role::query()->find($user->role_id);

            if ($role) {
                $user->syncRoles([$role->name]);
            }
        });
    }
}
