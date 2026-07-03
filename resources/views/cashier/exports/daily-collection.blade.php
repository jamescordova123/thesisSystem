@extends('cashier.layouts.print')

@section('title', 'Daily Collection Report')

@section('content')
    <h1>Daily Collection Report</h1>
    <p class="meta">{{ today()->format('F j, Y') }} · Total: ₱{{ number_format($payments->sum('amount'), 2) }}</p>

    <table>
        <thead>
            <tr>
                <th>OR Number</th>
                <th>Student</th>
                <th>Amount</th>
                <th>Method</th>
                <th>Cashier</th>
                <th>Time</th>
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
                    <td>{{ $payment->payment_date?->format('g:i A') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
