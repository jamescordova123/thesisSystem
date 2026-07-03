@extends('registrar.layouts.print')

@section('title', 'Academic History - ' . ($student->studentInformation?->full_name ?? $student->name))

@section('content')
    <h1>Academic History</h1>
    <p class="meta">
        {{ $student->studentInformation?->full_name ?? $student->name }} ·
        {{ $student->studentInformation?->student_number }} ·
        {{ $student->studentInformation?->academic_program }}
    </p>

    <table>
        <thead>
            <tr>
                <th>Subject</th>
                <th>Grade</th>
                <th>Term</th>
                <th>GPA</th>
                <th>GWA</th>
                <th>Standing</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($histories as $history)
                <tr>
                    <td>{{ $history->subject_code }} - {{ $history->subject_name }}</td>
                    <td>{{ $history->grade ?? '—' }}</td>
                    <td>{{ $history->academicYear?->label }} {{ $history->semester?->name }}</td>
                    <td>{{ $history->gpa ?? '—' }}</td>
                    <td>{{ $history->gwa ?? '—' }}</td>
                    <td>{{ $history->academic_standing ?? '—' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">No academic history records.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
