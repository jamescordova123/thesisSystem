<?php

namespace App\Enums;

enum CashierNotificationType: string
{
    case PaymentReminder = 'payment_reminder';
    case OutstandingBalance = 'outstanding_balance';
    case DueDateReminder = 'due_date_reminder';
    case PaymentConfirmation = 'payment_confirmation';
    case TuitionAnnouncement = 'tuition_announcement';
    case FinancialNotice = 'financial_notice';

    public function label(): string
    {
        return match ($this) {
            self::PaymentReminder => 'Payment Reminder',
            self::OutstandingBalance => 'Outstanding Balance Notice',
            self::DueDateReminder => 'Due Date Reminder',
            self::PaymentConfirmation => 'Payment Confirmation',
            self::TuitionAnnouncement => 'Tuition Announcement',
            self::FinancialNotice => 'Financial Notice',
        };
    }

    /**
     * @return array<array{value: string, label: string}>
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->map(fn (self $type) => ['value' => $type->value, 'label' => $type->label()])
            ->values()
            ->all();
    }
}
