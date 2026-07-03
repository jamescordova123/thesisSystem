<?php

namespace App\Http\Controllers\Registrar;

use App\Enums\AppRole;
use App\Enums\EnrollmentStatus;
use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Models\StudentInformation;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class ReportController extends Controller
{
    public function index(): Response
    {
        $enrolled = StudentInformation::where('enrollment_status', EnrollmentStatus::Enrolled->value)->count();
        $total = User::havingAppRole(AppRole::Student)->count();

        return Inertia::render('registrar/reports/index', [
            'reportTypes' => [
                ['key' => 'class-list', 'label' => 'Official Class List', 'route' => 'registrar.class-lists.index'],
                ['key' => 'enrollment-summary', 'label' => 'Enrollment Summary', 'count' => $enrolled],
                ['key' => 'student-master', 'label' => 'Student Master List', 'count' => $total],
                ['key' => 'students-per-section', 'label' => 'Students per Section', 'count' => Section::where('is_archived', false)->count()],
                ['key' => 'enrolled-vs-not', 'label' => 'Enrolled vs. Not Enrolled', 'enrolled' => $enrolled, 'not_enrolled' => max(0, $total - $enrolled)],
                ['key' => 'section-capacity', 'label' => 'Section Capacity Report'],
            ],
        ]);
    }
}
