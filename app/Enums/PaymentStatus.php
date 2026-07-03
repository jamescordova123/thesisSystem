<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case NoPayment = 'no_payment';
    case Partial = 'partial';
    case FullyPaid = 'fully_paid';

    public function label(): string
    {
        return match ($this) {
            self::NoPayment => 'No Payment',
            self::Partial => 'Partial',
            self::FullyPaid => 'Fully Paid',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::NoPayment => 'destructive',
            self::Partial => 'warning',
            self::FullyPaid => 'success',
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
