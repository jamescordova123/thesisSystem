@extends('registrar.layouts.print')

@section('title', 'Student Master List')

@section('content')
    <h1>Student Master List</h1>
    <p class="meta">Generated {{ now()->format('F j, Y g:i A') }}</p>

    <table>
        <thead>
            <tr>
                <th>Student ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Program</th>
                <th>Year</th>
                <th>Section</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($students as $student)
                <tr>
                    <td>{{ $student->studentInformation?->student_number }}</td>
                    <td>{{ $student->studentInformation?->full_name ?? $student->name }}</td>
                    <td>{{ $student->email }}</td>
                    <td>{{ $student->studentInformation?->program?->name ?? $student->studentInformation?->academic_program }}</td>
                    <td>{{ $student->studentInformation?->year_level ? 'Year ' . $student->studentInformation->year_level : '' }}</td>
                    <td>{{ $student->studentInformation?->section?->name }}</td>
                    <td>{{ str_replace('_', ' ', $student->studentInformation?->enrollment_status ?? '') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
