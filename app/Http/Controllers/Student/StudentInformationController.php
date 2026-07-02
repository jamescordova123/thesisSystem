<?php

namespace App\Http\Controllers\Student;

use App\Enums\AppRole;
use App\Enums\StudentSex;
use App\Enums\StudentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Student\SaveStudentInformationRequest;
use App\Models\StudentInformation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StudentInformationController extends Controller
{
    /**
     * Display the student's information sheet form.
     */
    public function edit(Request $request): Response
    {
        $this->ensureStudent($request);

        $information = $request->user()->studentInformation;

        return Inertia::render('student/information-sheet/index', [
            'information' => $information ? $this->toPayload($information) : null,
            'sexOptions' => StudentSex::options(),
            'statusOptions' => StudentStatus::options(),
        ]);
    }

    /**
     * Store or update the student's information sheet.
     */
    public function store(SaveStudentInformationRequest $request): RedirectResponse
    {
        $user = $request->user();
        $exists = $user->studentInformation()->exists();

        $user->studentInformation()->updateOrCreate(
            ['user_id' => $user->id],
            $request->validated(),
        );

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => $exists
                ? __('Information sheet updated successfully.')
                : __('Information sheet saved successfully.'),
        ]);

        return to_route('information-sheet.edit', ['current_team' => $request->route('current_team')]);
    }

    /**
     * @return array<string, mixed>
     */
    private function toPayload(StudentInformation $information): array
    {
        return [
            'school_year' => $information->school_year,
            'full_name' => $information->full_name,
            'academic_program' => $information->academic_program,
            'status' => $information->status,
            'birthdate' => $information->birthdate->format('Y-m-d'),
            'birthdate_display' => $information->birthdate->format('d/m/Y'),
            'sex' => $information->sex,
            'age' => $information->age,
            'place_of_birth' => $information->place_of_birth,
            'current_address' => $information->current_address,
            'permanent_address' => $information->permanent_address,
            'father_name' => $information->father_name ?? '',
            'mother_maiden_name' => $information->mother_maiden_name ?? '',
            'guardian_name' => $information->guardian_name,
            'primary_contact_number' => $information->primary_contact_number,
            'facebook_account' => $information->facebook_account ?? '',
            'facebook_profile' => $information->facebook_profile ?? '',
            'last_school_attended' => $information->last_school_attended ?? '',
            'previous_section' => $information->previous_section ?? '',
            'last_school_address' => $information->last_school_address ?? '',
            'academic_year' => $information->academic_year ?? '',
            'completion_date' => $information->completion_date?->format('Y-m-d') ?? '',
            'completion_date_display' => $information->completion_date?->format('d/m/Y') ?? '',
            'gwa' => $information->gwa !== null ? (string) $information->gwa : '',
        ];
    }

    /**
     * Ensure the authenticated user is a student.
     */
    private function ensureStudent(Request $request): void
    {
        if (! $request->user()?->hasRole(AppRole::Student->value)) {
            abort(403);
        }
    }
}
