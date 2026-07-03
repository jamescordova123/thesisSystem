<?php

namespace App\Http\Controllers\Registrar;

use App\Enums\AppRole;
use App\Http\Controllers\Controller;
use App\Models\AcademicHistory;
use App\Models\Section;
use App\Models\User;
use App\Services\RegistrarExportService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    public function __construct(
        private RegistrarExportService $exporter,
    ) {}

    public function classList(Request $request, Section $section): StreamedResponse|View
    {
        $section->load(['program', 'semester', 'academicYear', 'students.studentInformation']);

        if ($request->query('format') === 'csv') {
            return $this->exporter->classListCsv($section);
        }

        return view('registrar.exports.class-list', [
            'section' => $section,
            'students' => $section->students,
        ]);
    }

    public function studentMaster(Request $request): StreamedResponse|View
    {
        if ($request->query('format') === 'csv') {
            return $this->exporter->studentMasterCsv();
        }

        $students = User::havingAppRole(AppRole::Student)
            ->with(['studentInformation.program', 'studentInformation.section'])
            ->orderBy('name')
            ->get();

        return view('registrar.exports.student-master', compact('students'));
    }

    public function enrollmentSummary(Request $request): StreamedResponse
    {
        return $this->exporter->enrollmentSummaryCsv();
    }

    public function studentsPerSection(Request $request): StreamedResponse
    {
        return $this->exporter->studentsPerSectionCsv();
    }

    public function sectionCapacity(Request $request): StreamedResponse
    {
        return $this->exporter->sectionCapacityCsv();
    }

    public function enrolledVsNot(Request $request): StreamedResponse
    {
        return $this->exporter->enrolledVsNotCsv();
    }

    public function academicHistory(Request $request, User $user): StreamedResponse|View
    {
        $user->load('studentInformation');

        if ($request->query('format') === 'csv') {
            return $this->exporter->academicHistoryCsv($user);
        }

        $histories = AcademicHistory::query()
            ->where('user_id', $user->id)
            ->with(['academicYear', 'semester'])
            ->orderByDesc('created_at')
            ->get();

        return view('registrar.exports.academic-history', [
            'student' => $user,
            'histories' => $histories,
        ]);
    }
}
