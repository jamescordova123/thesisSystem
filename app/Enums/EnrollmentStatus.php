<?php

namespace App\Enums;

enum EnrollmentStatus: string
{
    case Enrolled = 'enrolled';
    case Pending = 'pending';
    case Incomplete = 'incomplete';
    case NotEnrolled = 'not_enrolled';

    public function label(): string
    {
        return match ($this) {
            self::Enrolled => 'Enrolled',
            self::Pending => 'Pending',
            self::Incomplete => 'Incomplete',
            self::NotEnrolled => 'Not Enrolled',
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
