<?php

namespace App\Http\Controllers\Registrar;

use App\Enums\AppRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Registrar\StoreStudentRecordRequest;
use App\Models\StudentRecord;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StudentRecordController extends Controller
{
    public function index(Request $request): Response
    {
        $students = User::havingAppRole(AppRole::Student)
            ->with('studentInformation')
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->string('search')->toString();
                $q->whereHas('studentInformation', fn ($sq) => $sq
                    ->where('student_number', 'like', "%{$search}%")
                    ->orWhere('full_name', 'like', "%{$search}%"));
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString()
            ->through(fn (User $u) => [
                'id' => $u->id,
                'student_number' => $u->studentInformation?->student_number,
                'name' => $u->studentInformation?->full_name ?? $u->name,
                'program' => $u->studentInformation?->academic_program,
                'records_count' => StudentRecord::where('user_id', $u->id)->count(),
            ]);

        return Inertia::render('registrar/records/index', [
            'students' => $students,
            'filters' => $request->only('search'),
        ]);
    }

    public function show(User $user): Response
    {
        $user->load('studentInformation');

        $records = StudentRecord::query()
            ->where('user_id', $user->id)
            ->with('uploader')
            ->latest()
            ->get()
            ->map(fn (StudentRecord $r) => [
                'id' => $r->id,
                'title' => $r->title,
                'record_type' => $r->record_type,
                'notes' => $r->notes,
                'has_file' => (bool) $r->file_path,
                'uploaded_by' => $r->uploader?->name,
                'created_at' => $r->created_at?->toIso8601String(),
            ]);

        return Inertia::render('registrar/records/show', [
            'student' => [
                'id' => $user->id,
                'student_number' => $user->studentInformation?->student_number,
                'name' => $user->studentInformation?->full_name ?? $user->name,
            ],
            'records' => $records,
            'recordTypes' => [
                'transcript',
                'enrollment_requirement',
                'medical',
                'identification',
                'other',
            ],
        ]);
    }

    public function store(StoreStudentRecordRequest $request, User $user): RedirectResponse
    {
        $filePath = null;

        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store("student-records/{$user->id}", 'local');
        }

        $record = StudentRecord::create([
            'user_id' => $user->id,
            'title' => $request->validated('title'),
            'record_type' => $request->validated('record_type'),
            'file_path' => $filePath,
            'notes' => $request->validated('notes'),
            'uploaded_by' => $request->user()->id,
        ]);

        AuditLogger::log('student_record.uploaded', $record, null, $record->toArray());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Student record saved.')]);

        return back();
    }

    public function download(User $user, StudentRecord $record): StreamedResponse
    {
        abort_unless($record->user_id === $user->id, 404);
        abort_unless($record->file_path && Storage::disk('local')->exists($record->file_path), 404);

        return Storage::disk('local')->download($record->file_path, $record->title);
    }
}
