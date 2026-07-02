<?php

namespace App\Enums;

enum StudentStatus: string
{
    case Regular = 'regular';
    case Irregular = 'irregular';
    case Transferred = 'transferred';
    case Graduated = 'graduated';
    case Dropped = 'dropped';

    public function label(): string
    {
        return match ($this) {
            self::Regular => 'Regular',
            self::Irregular => 'Irregular',
            self::Transferred => 'Transferred',
            self::Graduated => 'Graduated',
            self::Dropped => 'Dropped',
        };
    }

    /**
     * @return array<array{value: string, label: string}>
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->map(fn (self $status) => ['value' => $status->value, 'label' => $status->label()])
            ->values()
            ->all();
    }
}
