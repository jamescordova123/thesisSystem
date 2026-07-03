<?php

namespace App\Services;

use App\Enums\AppRole;
use App\Enums\EnrollmentStatus;
use App\Models\AcademicHistory;
use App\Models\Section;
use App\Models\StudentInformation;
use App\Models\User;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RegistrarExportService
{
    /**
     * @param  array<int, string>  $headers
     * @param  iterable<int, array<int, string|int|float|null>>  $rows
     */
    public function csv(string $filename, array $headers, iterable $rows): StreamedResponse
    {
        return response()->streamDownload(function () use ($headers, $rows) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $headers);

            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function classListCsv(Section $section): StreamedResponse
    {
        $section->load(['program', 'semester', 'academicYear', 'students.studentInformation']);

        $rows = $section->students->values()->map(
            fn (User $user, int $index) => [
                $index + 1,
                $user->studentInformation?->student_number ?? '',
                $user->studentInformation?->full_name ?? $user->name,
                $user->studentInformation?->enrollment_status ?? '',
            ],
        );

        return $this->csv(
            "class-list-{$section->name}.csv",
            ['#', 'Student ID', 'Name', 'Enrollment Status'],
            $rows,
        );
    }

    public function studentMasterCsv(): StreamedResponse
    {
        $students = User::havingAppRole(AppRole::Student)
            ->with(['studentInformation.program', 'studentInformation.section'])
            ->orderBy('name')
            ->get();

        $rows = $students->map(fn (User $user) => [
            $user->studentInformation?->student_number ?? '',
            $user->studentInformation?->full_name ?? $user->name,
            $user->email,
            $user->studentInformation?->program?->name ?? $user->studentInformation?->academic_program ?? '',
            $user->studentInformation?->year_level ?? '',
            $user->studentInformation?->section?->name ?? '',
            $user->studentInformation?->enrollment_status ?? '',
        ]);

        return $this->csv(
            'student-master-list.csv',
            ['Student ID', 'Name', 'Email', 'Program', 'Year Level', 'Section', 'Enrollment Status'],
            $rows,
        );
    }

    public function enrollmentSummaryCsv(): StreamedResponse
    {
        $rows = StudentInformation::query()
            ->with(['user', 'program', 'section'])
            ->orderBy('full_name')
            ->get()
            ->map(fn (StudentInformation $info) => [
                $info->student_number ?? '',
                $info->full_name ?? $info->user?->name ?? '',
                $info->program?->name ?? $info->academic_program ?? '',
                $info->section?->name ?? '',
                $info->enrollment_status ?? '',
                $info->school_year ?? '',
            ]);

        return $this->csv(
            'enrollment-summary.csv',
            ['Student ID', 'Name', 'Program', 'Section', 'Status', 'School Year'],
            $rows,
        );
    }

    public function studentsPerSectionCsv(): StreamedResponse
    {
        $sections = Section::query()
            ->with(['program', 'semester', 'academicYear'])
            ->withCount('students')
            ->where('is_archived', false)
            ->orderBy('name')
            ->get();

        $rows = $sections->map(fn (Section $section) => [
            $section->name,
            $section->program->name,
            $section->year_level,
            $section->semester->name,
            $section->academicYear->label,
            $section->students_count,
            $section->max_capacity,
            max(0, $section->max_capacity - $section->students_count),
        ]);

        return $this->csv(
            'students-per-section.csv',
            ['Section', 'Program', 'Year Level', 'Semester', 'Academic Year', 'Students', 'Capacity', 'Remaining'],
            $rows,
        );
    }

    public function sectionCapacityCsv(): StreamedResponse
    {
        return $this->studentsPerSectionCsv();
    }

    public function enrolledVsNotCsv(): StreamedResponse
    {
        $students = User::havingAppRole(AppRole::Student)
            ->with('studentInformation')
            ->orderBy('name')
            ->get();

        $rows = $students->map(fn (User $user) => [
            $user->studentInformation?->student_number ?? '',
            $user->studentInformation?->full_name ?? $user->name,
            ($user->studentInformation?->enrollment_status === EnrollmentStatus::Enrolled->value) ? 'Enrolled' : 'Not Enrolled',
        ]);

        return $this->csv(
            'enrolled-vs-not-enrolled.csv',
            ['Student ID', 'Name', 'Category'],
            $rows,
        );
    }

    public function academicHistoryCsv(User $user): StreamedResponse
    {
        $user->load('studentInformation');

        $histories = AcademicHistory::query()
            ->where('user_id', $user->id)
            ->with(['academicYear', 'semester'])
            ->orderByDesc('created_at')
            ->get();

        $rows = $histories->map(fn (AcademicHistory $history) => [
            $history->subject_code,
            $history->subject_name,
            $history->grade,
            $history->academicYear?->label ?? '',
            $history->semester?->name ?? '',
            $history->gpa,
            $history->gwa,
            $history->academic_standing,
        ]);

        $name = $user->studentInformation?->student_number ?? $user->id;

        return $this->csv(
            "academic-history-{$name}.csv",
            ['Subject Code', 'Subject Name', 'Grade', 'Academic Year', 'Semester', 'GPA', 'GWA', 'Standing'],
            $rows,
        );
    }
}
