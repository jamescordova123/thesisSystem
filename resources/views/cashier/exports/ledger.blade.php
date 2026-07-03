@extends('cashier.layouts.print')

@section('title', 'Financial Ledger - ' . ($ledger['full_name'] ?? ''))

@section('content')
    <h1>Student Financial Ledger</h1>
    <p class="meta">
        {{ $ledger['full_name'] }} · {{ $ledger['student_number'] }} ·
        {{ $ledger['program'] }} · Year {{ $ledger['year_level'] }} · {{ $ledger['section'] }}
    </p>
    <p class="meta">{{ $ledger['academic_year'] }} · {{ $ledger['semester'] }}</p>

    <h2 style="font-size:1rem;margin:1.5rem 0 0.5rem;">Fee Assessment</h2>
    <table>
        <tr><th>Tuition</th><td>₱{{ number_format($ledger['fees']['tuition'], 2) }}</td></tr>
        <tr><th>Test Paper Fee</th><td>₱{{ number_format($ledger['fees']['test_paper_fee'], 2) }}</td></tr>
        <tr><th>PTA Fee</th><td>₱{{ number_format($ledger['fees']['pta_fee'], 2) }}</td></tr>
        <tr><th>Uniform Fee</th><td>₱{{ number_format($ledger['fees']['uniform_fee'], 2) }}</td></tr>
        <tr><th>Certificates Fee</th><td>₱{{ number_format($ledger['fees']['certificates_fee'], 2) }}</td></tr>
        <tr><th>Graduation Fee</th><td>₱{{ number_format($ledger['fees']['graduation_fee'], 2) }}</td></tr>
        <tr><th>Total Assessment</th><td><strong>₱{{ number_format($ledger['total_assessment'], 2) }}</strong></td></tr>
        <tr><th>Total Payments</th><td>₱{{ number_format($ledger['total_payments'], 2) }}</td></tr>
        <tr><th>Remaining Balance</th><td>₱{{ number_format($ledger['remaining_balance'], 2) }}</td></tr>
        <tr><th>Account Standing</th><td>{{ $ledger['payment_status_label'] }}</td></tr>
    </table>

    <h2 style="font-size:1rem;margin:1.5rem 0 0.5rem;">Payment History</h2>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>OR Number</th>
                <th>Amount</th>
                <th>Method</th>
                <th>Cashier</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($ledger['payments'] as $payment)
                <tr>
                    <td>{{ $payment['payment_date'] ? \Carbon\Carbon::parse($payment['payment_date'])->format('M j, Y') : '' }}</td>
                    <td>{{ $payment['official_receipt_number'] }}</td>
                    <td>₱{{ number_format($payment['amount'], 2) }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $payment['payment_method'])) }}</td>
                    <td>{{ $payment['cashier'] }}</td>
                </tr>
            @empty
                <tr><td colspan="5">No payments recorded.</td></tr>
            @endforelse
        </tbody>
    </table>
@endsection
