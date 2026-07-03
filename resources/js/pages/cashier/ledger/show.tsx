import { Head, Link } from '@inertiajs/react';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { ledger as exportLedger } from '@/routes/cashier/exports';
import { show as paymentShow } from '@/routes/cashier/payments';

type Ledger = {
    id: number;
    student_number: string | null;
    full_name: string;
    program: string | null;
    year_level: number | null;
    section: string | null;
    academic_year: string;
    semester: string;
    fees: Record<string, number>;
    total_assessment: number;
    total_payments: number;
    remaining_balance: number;
    payment_status: string;
    payment_status_label: string;
    payments: {
        id: number;
        payment_date: string | null;
        official_receipt_number: string;
        amount: number;
        payment_method: string;
        cashier: string | null;
        remarks: string | null;
    }[];
};

type Props = { ledger: Ledger };

const formatCurrency = (v: number) =>
    new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' }).format(v);

const feeLabels: Record<string, string> = {
    tuition: 'Tuition', test_paper_fee: 'Test Paper Fee', pta_fee: 'PTA Fee',
    uniform_fee: 'Uniform Fee', certificates_fee: 'Certificates Fee', graduation_fee: 'Graduation Fee',
};

export default function LedgerShow({ ledger }: Props) {
    const statusVariant = ledger.payment_status === 'fully_paid' ? 'default' : ledger.payment_status === 'partial' ? 'secondary' : 'destructive';

    return (
        <>
            <Head title={`Ledger - ${ledger.full_name}`} />
            <div className="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between print:hidden">
                <div>
                    <h2 className="text-lg font-semibold">Financial Ledger</h2>
                    <p className="text-sm text-muted-foreground">{ledger.full_name} · {ledger.student_number}</p>
                </div>
                <div className="flex gap-2">
                    <Badge variant={statusVariant}>{ledger.payment_status_label}</Badge>
                    <Button variant="outline" size="sm" asChild>
                        <a href={exportLedger(ledger.id).url} target="_blank" rel="noreferrer">Print / PDF</a>
                    </Button>
                    <Button variant="secondary" size="sm" asChild>
                        <a href={`${exportLedger(ledger.id).url}?format=csv`}>Export Excel</a>
                    </Button>
                    <Button variant="ghost" size="sm" asChild>
                        <Link href={paymentShow(ledger.id)}>Record Payment</Link>
                    </Button>
                </div>
            </div>

            <div className="mb-4 grid gap-4 text-sm sm:grid-cols-2">
                <div><span className="text-muted-foreground">Program:</span> {ledger.program}</div>
                <div><span className="text-muted-foreground">Year / Section:</span> Year {ledger.year_level} · {ledger.section}</div>
                <div><span className="text-muted-foreground">Academic Year:</span> {ledger.academic_year}</div>
                <div><span className="text-muted-foreground">Semester:</span> {ledger.semester}</div>
            </div>

            <div className="mb-6 grid gap-4 sm:grid-cols-2">
                <div className="rounded-lg border p-4">
                    <h3 className="mb-2 font-medium">Fees</h3>
                    {Object.entries(ledger.fees).map(([key, amount]) => (
                        <div key={key} className="flex justify-between py-1 text-sm">
                            <span>{feeLabels[key]}</span><span>{formatCurrency(amount)}</span>
                        </div>
                    ))}
                </div>
                <div className="rounded-lg border p-4">
                    <h3 className="mb-2 font-medium">Summary</h3>
                    <div className="flex justify-between py-1 text-sm"><span>Total Assessment</span><span>{formatCurrency(ledger.total_assessment)}</span></div>
                    <div className="flex justify-between py-1 text-sm"><span>Total Payments</span><span>{formatCurrency(ledger.total_payments)}</span></div>
                    <div className="flex justify-between py-1 text-sm font-semibold"><span>Remaining Balance</span><span>{formatCurrency(ledger.remaining_balance)}</span></div>
                </div>
            </div>

            <table className="w-full text-sm border rounded-lg overflow-hidden">
                <thead className="bg-muted/50">
                    <tr>
                        <th className="px-3 py-2 text-left">Date</th>
                        <th className="px-3 py-2 text-left">OR Number</th>
                        <th className="px-3 py-2 text-left">Amount</th>
                        <th className="px-3 py-2 text-left">Method</th>
                        <th className="px-3 py-2 text-left">Cashier</th>
                        <th className="px-3 py-2 text-left">Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    {ledger.payments.map((p) => (
                        <tr key={p.id} className="border-t">
                            <td className="px-3 py-2">{p.payment_date ? new Date(p.payment_date).toLocaleString() : '—'}</td>
                            <td className="px-3 py-2 font-mono text-xs">{p.official_receipt_number}</td>
                            <td className="px-3 py-2">{formatCurrency(p.amount)}</td>
                            <td className="px-3 py-2 capitalize">{p.payment_method.replace('_', ' ')}</td>
                            <td className="px-3 py-2">{p.cashier ?? '—'}</td>
                            <td className="px-3 py-2">{p.remarks ?? '—'}</td>
                        </tr>
                    ))}
                </tbody>
            </table>
        </>
    );
}

LedgerShow.layout = (props: Props) => ({
    breadcrumbs: [{ title: 'Ledger', href: '#' }, { title: props.ledger.full_name, href: '#' }],
});
