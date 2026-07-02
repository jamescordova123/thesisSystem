<?php

namespace App\Enums;

enum AppRole: string
{
    case SuperAdmin = 'super-admin';
    case Admin = 'admin';
    case Student = 'student';
    case Advisor = 'advisor';

    /**
     * Get the display label for the role.
     */
    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Super Admin',
            self::Admin => 'Admin',
            self::Student => 'Student',
            self::Advisor => 'Advisor',
        };
    }

    /**
     * @return array<array{value: string, label: string}>
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->map(fn (self $role) => ['value' => $role->value, 'label' => $role->label()])
            ->values()
            ->all();
    }
}
