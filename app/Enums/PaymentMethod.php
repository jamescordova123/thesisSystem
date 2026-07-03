<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case Cash = 'cash';
    case Check = 'check';
    case BankTransfer = 'bank_transfer';
    case Online = 'online';

    public function label(): string
    {
        return match ($this) {
            self::Cash => 'Cash',
            self::Check => 'Check',
            self::BankTransfer => 'Bank Transfer',
            self::Online => 'Online',
        };
    }

    /**
     * @return array<array{value: string, label: string}>
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->map(fn (self $method) => ['value' => $method->value, 'label' => $method->label()])
            ->values()
            ->all();
    }
}
