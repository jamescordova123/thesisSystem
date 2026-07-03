@extends('registrar.layouts.print')

@section('title', 'Official Class List - ' . $section->name)

@section('content')
    <h1>Official Class List</h1>
    <p class="meta">
        {{ $section->name }} · {{ $section->program->name }} · Year {{ $section->year_level }} ·
        {{ $section->semester->name }} · {{ $section->academicYear->label }}
    </p>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Student ID</th>
                <th>Name</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($students as $index => $student)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $student->studentInformation?->student_number }}</td>
                    <td>{{ $student->studentInformation?->full_name ?? $student->name }}</td>
                    <td>{{ str_replace('_', ' ', $student->studentInformation?->enrollment_status ?? '') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
