<?php

namespace App\Models;

use App\Enums\AppRole;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Permission\Models\Role as SpatieRole;

/**
 * @property int $id
 * @property string $name
 * @property string $guard_name
 * @property-read Collection<int, User> $assignedUsers
 */
class Role extends SpatieRole
{
    /**
     * Get the users assigned to this role through the role_id foreign key.
     *
     * @return HasMany<User, $this>
     */
    public function assignedUsers(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Resolve a role id from the application role enum.
     */
    public static function idFor(AppRole $role): int
    {
        return static::findByName($role->value, 'web')->id;
    }
}
