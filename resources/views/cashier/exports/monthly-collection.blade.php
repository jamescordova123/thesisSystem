@extends('cashier.layouts.print')

@section('title', 'Monthly Collection Report')

@section('content')
    <h1>Monthly Collection Report</h1>
    <p class="meta">{{ now()->format('F Y') }} · Total: ₱{{ number_format($payments->sum('amount'), 2) }}</p>

    <table>
        <thead>
            <tr>
                <th>OR Number</th>
                <th>Student</th>
                <th>Amount</th>
                <th>Method</th>
                <th>Cashier</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($payments as $payment)
                <tr>
                    <td>{{ $payment->official_receipt_number }}</td>
                    <td>{{ $payment->user->studentInformation?->full_name ?? $payment->user->name }}</td>
                    <td>₱{{ number_format($payment->amount, 2) }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td>
                    <td>{{ $payment->cashier?->name }}</td>
                    <td>{{ $payment->payment_date?->format('M j, Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
