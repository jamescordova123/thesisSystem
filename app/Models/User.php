<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\AppRole;
use App\Concerns\HasAppPermissions;
use App\Concerns\HasAppRole;
use App\Concerns\HasTeams;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Laravel\Fortify\Contracts\PasskeyUser;
use Laravel\Fortify\PasskeyAuthenticatable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property Carbon|null $two_factor_confirmed_at
 * @property string|null $remember_token
 * @property int|null $current_team_id
 * @property int|null $role_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Role|null $role
 * @property-read Team|null $currentTeam
 * @property-read Collection<int, Team> $ownedTeams
 * @property-read Collection<int, Membership> $teamMemberships
 * @property-read Collection<int, Team> $teams
 * @property-read Collection<int, Announcement> $receivedAnnouncements
 * @property-read StudentInformation|null $studentInformation
 */
#[Fillable(['name', 'email', 'password', 'current_team_id', 'role_id'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable implements PasskeyUser
{
    /** @use HasFactory<UserFactory> */
    use HasAppPermissions, HasAppRole, HasFactory, Notifiable, PasskeyAuthenticatable, TwoFactorAuthenticatable;
    use HasRoles, HasTeams {
        HasTeams::teams insteadof HasRoles;
    }

    /**
     * Get the announcements addressed to this user.
     *
     * @return BelongsToMany<Announcement, $this>
     */
    public function receivedAnnouncements(): BelongsToMany
    {
        return $this->belongsToMany(Announcement::class, 'announcement_user')
            ->withPivot('read_at')
            ->withTimestamps();
    }

    /**
     * Get the student's information sheet.
     *
     * @return HasOne<StudentInformation, $this>
     */
    public function studentInformation(): HasOne
    {
        return $this->hasOne(StudentInformation::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<StudentNotification, $this>
     */
    public function studentNotifications(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(StudentNotification::class);
    }

    /**
     * Scope users that have the given application role.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<User>  $query
     */
    public function scopeHavingAppRole($query, AppRole|string $role): void
    {
        $roleName = $role instanceof AppRole ? $role->value : $role;

        $query->whereHas('roles', fn ($q) => $q->where('name', $roleName));
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }
}
