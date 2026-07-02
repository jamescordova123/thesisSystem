<?php

namespace App\Enums;

enum StudentSex: string
{
    case Male = 'male';
    case Female = 'female';

    public function label(): string
    {
        return match ($this) {
            self::Male => 'Male',
            self::Female => 'Female',
        };
    }

    /**
     * @return array<array{value: string, label: string}>
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->map(fn (self $sex) => ['value' => $sex->value, 'label' => $sex->label()])
            ->values()
            ->all();
    }
}
