@extends('cashier.layouts.print')

@section('title', 'Official Receipt - ' . $payment->official_receipt_number)

@section('content')
    <h1>Official Receipt</h1>
    <p class="meta">OR No: {{ $payment->official_receipt_number }} · {{ $payment->payment_date?->format('F j, Y g:i A') }}</p>

    <table>
        <tr><th>Student</th><td>{{ $payment->user->studentInformation?->full_name ?? $payment->user->name }}</td></tr>
        <tr><th>Student ID</th><td>{{ $payment->user->studentInformation?->student_number }}</td></tr>
        <tr><th>Program</th><td>{{ $payment->user->studentInformation?->academic_program }}</td></tr>
        <tr><th>Academic Year</th><td>{{ $payment->assessment->academicYear->label }}</td></tr>
        <tr><th>Semester</th><td>{{ $payment->assessment->semester->name }}</td></tr>
        <tr><th>Amount Paid</th><td><strong>₱{{ number_format($payment->amount, 2) }}</strong></td></tr>
        <tr><th>Payment Method</th><td>{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td></tr>
        <tr><th>Cashier</th><td>{{ $payment->cashier?->name }}</td></tr>
        @if ($payment->remarks)
            <tr><th>Remarks</th><td>{{ $payment->remarks }}</td></tr>
        @endif
    </table>
@endsection
