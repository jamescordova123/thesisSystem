<?php

namespace App\Http\Controllers\Registrar;

use App\Enums\AppRole;
use App\Enums\EnrollmentStatus;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Enrollment;
use App\Models\Section;
use App\Models\StudentInformation;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $totalStudents = User::havingAppRole(AppRole::Student)->count();
        $enrolledStudents = StudentInformation::query()
            ->where('enrollment_status', EnrollmentStatus::Enrolled->value)
            ->where('is_archived', false)
            ->count();
        $totalSections = Section::query()->where('is_archived', false)->count();

        $recentEnrollments = Enrollment::query()
            ->with(['user.studentInformation', 'program', 'section'])
            ->latest()
            ->limit(8)
            ->get()
            ->map(fn (Enrollment $enrollment) => [
                'id' => $enrollment->id,
                'student' => $enrollment->user->studentInformation?->full_name ?? $enrollment->user->name,
                'student_number' => $enrollment->user->studentInformation?->student_number,
                'program' => $enrollment->program->name,
                'section' => $enrollment->section?->name,
                'status' => $enrollment->status,
                'enrolled_at' => $enrollment->enrolled_at?->toIso8601String(),
            ]);

        $recentActivity = AuditLog::query()
            ->with('user')
            ->latest()
            ->limit(6)
            ->get()
            ->map(fn (AuditLog $log) => [
                'action' => $log->action,
                'user' => $log->user?->name,
                'created_at' => $log->created_at?->toIso8601String(),
            ]);

        return Inertia::render('registrar/dashboard', [
            'stats' => [
                'totalStudents' => $totalStudents,
                'enrolledStudents' => $enrolledStudents,
                'notEnrolledStudents' => max(0, $totalStudents - $enrolledStudents),
                'totalSections' => $totalSections,
            ],
            'recentEnrollments' => $recentEnrollments,
            'recentActivity' => $recentActivity,
        ]);
    }
}
