<?php

namespace App\Services;

use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\StudentFeeAssessment;
use App\Models\User;

class FeeAssessmentService
{
    /**
     * Default fee structure for new assessments.
     *
     * @return array<string, float>
     */
    public static function defaultFees(): array
    {
        return [
            'tuition' => 15000.00,
            'test_paper_fee' => 500.00,
            'pta_fee' => 300.00,
            'uniform_fee' => 1200.00,
            'certificates_fee' => 400.00,
            'graduation_fee' => 0.00,
        ];
    }

    public static function getOrCreateForUser(
        User $user,
        ?int $academicYearId = null,
        ?int $semesterId = null,
    ): StudentFeeAssessment {
        $academicYearId ??= AcademicYear::query()->where('is_active', true)->orderByDesc('label')->value('id');
        $semesterId ??= Semester::query()->where('is_active', true)->orderBy('sort_order')->value('id');

        return StudentFeeAssessment::query()->firstOrCreate(
            [
                'user_id' => $user->id,
                'academic_year_id' => $academicYearId,
                'semester_id' => $semesterId,
            ],
            self::defaultFees(),
        );
    }
}
