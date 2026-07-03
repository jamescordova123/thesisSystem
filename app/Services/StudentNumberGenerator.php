<?php

namespace App\Services;

use App\Models\StudentInformation;

class StudentNumberGenerator
{
    public static function generate(): string
    {
        $year = now()->format('Y');
        $prefix = "STU-{$year}-";

        $last = StudentInformation::query()
            ->where('student_number', 'like', "{$prefix}%")
            ->orderByDesc('student_number')
            ->value('student_number');

        $sequence = 1;

        if ($last && preg_match('/-(\d+)$/', $last, $matches)) {
            $sequence = (int) $matches[1] + 1;
        }

        return $prefix.str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
    }
}
