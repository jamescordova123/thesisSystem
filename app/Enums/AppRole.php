<?php

namespace App\Enums;

enum AppRole: string
{
    case SuperAdmin = 'super-admin';
    case Registrar = 'registrar';
    case Cashier = 'cashier';
    case Student = 'student';

    /**
     * Get the display label for the role.
     */
    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Super Admin',
            self::Registrar => 'Registrar',
            self::Cashier => 'Cashier',
            self::Student => 'Student',
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
