<?php

namespace App\Services;

use App\Models\Payment;

class ReceiptNumberGenerator
{
    public static function generate(): string
    {
        $year = now()->format('Y');
        $prefix = "OR-{$year}-";

        $last = Payment::query()
            ->where('official_receipt_number', 'like', "{$prefix}%")
            ->orderByDesc('official_receipt_number')
            ->value('official_receipt_number');

        $sequence = 1;

        if ($last && preg_match('/-(\d+)$/', $last, $matches)) {
            $sequence = (int) $matches[1] + 1;
        }

        return $prefix.str_pad((string) $sequence, 5, '0', STR_PAD_LEFT);
    }
}
