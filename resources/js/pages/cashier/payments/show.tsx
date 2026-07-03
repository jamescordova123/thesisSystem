import { Head, Link, router, useForm } from '@inertiajs/react';
import { useState } from 'react';
import InputError from '@/components/input-error';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { Textarea } from '@/components/ui/textarea';
import { ledger as exportLedger } from '@/routes/cashier/exports';
import { receipt } from '@/routes/cashier/exports';
import { show as ledgerShow } from '@/routes/cashier/ledger';
import { index, show, store, update } from '@/routes/cashier/payments';
import type { SelectOption } from '@/types';

type Payment = {
    id: number;
    payment_date: string | null;
    official_receipt_number: string;
    amount: number;
    payment_method: string;
    cashier: string | null;
    remarks: string | null;
};

type Student = {
    id: number;
    student_number: string | null;
    full_name: string;
    program: string | null;
    year_level: number | null;
    section: string | null;
    academic_year: string;
    semester: string;
    assessment_id: number;
    fees: Record<string, number>;
    total_assessment: number;
    total_payments: number;
    remaining_balance: number;
    payment_status: string;
    payment_status_label: string;
    payments: Payment[];
};

type Props = { student: Student; paymentMethods: SelectOption[] };

const selectClass = 'border-input flex h-9 w-full rounded-md border px-3 py-1 text-sm';
const formatCurrency = (v: number) =>
    new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' }).format(v);

const feeLabels: Record<string, string> = {
    tuition: 'Tuition',
    test_paper_fee: 'Test Paper Fee',
    pta_fee: 'PTA Fee',
    uniform_fee: 'Uniform Fee',
    certificates_fee: 'Certificates Fee',
    graduation_fee: 'Graduation Fee',
};

export default function PaymentShow({ student, paymentMethods }: Props) {
    const [open, setOpen] = useState(false);
    const [editPayment, setEditPayment] = useState<Payment | null>(null);

    const payForm = useForm({
        student_fee_assessment_id: String(student.assessment_id),
        amount: '',
        payment_method: 'cash',
        payment_date: new Date().toISOString().slice(0, 16),
        remarks: '',
    });

    const editForm = useForm({
        amount: '',
        payment_method: 'cash',
        payment_date: '',
        remarks: '',
        official_receipt_number: '',
    });

    const submitPayment = (e: React.FormEvent) => {
        e.preventDefault();
        payForm.post(store(student.id).url, {
            onSuccess: () => { setOpen(false); payForm.reset(); },
        });
    };

    const submitEdit = (e: React.FormEvent) => {
        e.preventDefault();
        if (!editPayment) return;
        editForm.put(update({ user: student.id, payment: editPayment.id }).url, {
            onSuccess: () => { setEditPayment(null); editForm.reset(); },
        });
    };

    const statusVariant =
        student.payment_status === 'fully_paid' ? 'default'
        : student.payment_status === 'partial' ? 'secondary' : 'destructive';

    return (
        <>
            <Head title={`Payments - ${student.full_name}`} />

            <div className="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 className="text-lg font-semibold">{student.full_name}</h2>
                    <p className="text-sm text-muted-foreground">
                        {student.student_number} · {student.program} · Year {student.year_level} · {student.section}
                    </p>
                </div>
                <div className="flex flex-wrap gap-2">
                    <Badge variant={statusVariant}>{student.payment_status_label}</Badge>
                    <Button variant="outline" size="sm" asChild>
                        <a href={exportLedger(student.id).url} target="_blank" rel="noreferrer">Print Ledger</a>
                    </Button>
                    <Button variant="outline" size="sm" asChild>
                        <Link href={ledgerShow(student.id)}>View Ledger</Link>
                    </Button>
                    {student.remaining_balance > 0 && (
                        <Dialog open={open} onOpenChange={setOpen}>
                            <DialogTrigger asChild>
                                <Button size="sm">Record Payment</Button>
                            </DialogTrigger>
                            <DialogContent>
                                <DialogHeader><DialogTitle>Record Payment</DialogTitle></DialogHeader>
                                <form onSubmit={submitPayment} className="grid gap-3">
                                    <div>
                                        <Label>Amount (max {formatCurrency(student.remaining_balance)})</Label>
                                        <Input type="number" step="0.01" value={payForm.data.amount} onChange={(e) => payForm.setData('amount', e.target.value)} required />
                                        <InputError message={payForm.errors.amount} />
                                    </div>
                                    <div>
                                        <Label>Payment Method</Label>
                                        <select className={selectClass} value={payForm.data.payment_method} onChange={(e) => payForm.setData('payment_method', e.target.value)} required>
                                            {paymentMethods.map((m) => <option key={m.value} value={m.value}>{m.label}</option>)}
                                        </select>
                                    </div>
                                    <div>
                                        <Label>Payment Date</Label>
                                        <Input type="datetime-local" value={payForm.data.payment_date} onChange={(e) => payForm.setData('payment_date', e.target.value)} required />
                                    </div>
                                    <div>
                                        <Label>Remarks</Label>
                                        <Textarea value={payForm.data.remarks} onChange={(e) => payForm.setData('remarks', e.target.value)} />
                                    </div>
                                    <Button type="submit" disabled={payForm.processing}>
                                        {payForm.processing && <Spinner />} Save Payment
                                    </Button>
                                </form>
                            </DialogContent>
                        </Dialog>
                    )}
                </div>
            </div>

            <div className="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div className="rounded-lg border p-4">
                    <p className="text-sm text-muted-foreground">Total Assessment</p>
                    <p className="text-xl font-bold">{formatCurrency(student.total_assessment)}</p>
                </div>
                <div className="rounded-lg border p-4">
                    <p className="text-sm text-muted-foreground">Total Payments</p>
                    <p className="text-xl font-bold">{formatCurrency(student.total_payments)}</p>
                </div>
                <div className="rounded-lg border p-4">
                    <p className="text-sm text-muted-foreground">Remaining Balance</p>
                    <p className="text-xl font-bold text-destructive">{formatCurrency(student.remaining_balance)}</p>
                </div>
            </div>

            <div className="mb-6 rounded-lg border p-4">
                <h3 className="mb-3 font-medium">Fee Breakdown</h3>
                <div className="grid gap-2 sm:grid-cols-2">
                    {Object.entries(student.fees).map(([key, amount]) => (
                        <div key={key} className="flex justify-between text-sm">
                            <span>{feeLabels[key] ?? key}</span>
                            <span>{formatCurrency(amount)}</span>
                        </div>
                    ))}
                </div>
            </div>

            <h3 className="mb-3 font-medium">Payment History</h3>
            <div className="overflow-x-auto rounded-lg border">
                <table className="w-full text-sm">
                    <thead className="bg-muted/50">
                        <tr>
                            <th className="px-4 py-2 text-left">Date</th>
                            <th className="px-4 py-2 text-left">OR Number</th>
                            <th className="px-4 py-2 text-left">Amount</th>
                            <th className="px-4 py-2 text-left">Method</th>
                            <th className="px-4 py-2 text-left">Cashier</th>
                            <th className="px-4 py-2 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        {student.payments.map((payment) => (
                            <tr key={payment.id} className="border-t">
                                <td className="px-4 py-2">{payment.payment_date ? new Date(payment.payment_date).toLocaleString() : '—'}</td>
                                <td className="px-4 py-2 font-mono text-xs">{payment.official_receipt_number}</td>
                                <td className="px-4 py-2">{formatCurrency(payment.amount)}</td>
                                <td className="px-4 py-2 capitalize">{payment.payment_method.replace('_', ' ')}</td>
                                <td className="px-4 py-2">{payment.cashier ?? '—'}</td>
                                <td className="px-4 py-2 text-right">
                                    <div className="flex justify-end gap-1">
                                        <Button variant="ghost" size="sm" asChild>
                                            <a href={receipt(payment.id).url} target="_blank" rel="noreferrer">Receipt</a>
                                        </Button>
                                        <Button variant="ghost" size="sm" onClick={() => {
                                            setEditPayment(payment);
                                            editForm.setData({
                                                amount: String(payment.amount),
                                                payment_method: payment.payment_method,
                                                payment_date: payment.payment_date?.slice(0, 16) ?? '',
                                                remarks: payment.remarks ?? '',
                                                official_receipt_number: payment.official_receipt_number,
                                            });
                                        }}>Edit</Button>
                                    </div>
                                </td>
                            </tr>
                        ))}
                        {student.payments.length === 0 && (
                            <tr><td colSpan={6} className="px-4 py-6 text-center text-muted-foreground">No payments yet.</td></tr>
                        )}
                    </tbody>
                </table>
            </div>

            <Dialog open={!!editPayment} onOpenChange={(v) => !v && setEditPayment(null)}>
                <DialogContent>
                    <DialogHeader><DialogTitle>Edit Payment</DialogTitle></DialogHeader>
                    <form onSubmit={submitEdit} className="grid gap-3">
                        <div>
                            <Label>OR Number</Label>
                            <Input value={editForm.data.official_receipt_number} onChange={(e) => editForm.setData('official_receipt_number', e.target.value)} required />
                            <InputError message={editForm.errors.official_receipt_number} />
                        </div>
                        <div>
                            <Label>Amount</Label>
                            <Input type="number" step="0.01" value={editForm.data.amount} onChange={(e) => editForm.setData('amount', e.target.value)} required />
                        </div>
                        <div>
                            <Label>Payment Method</Label>
                            <select className={selectClass} value={editForm.data.payment_method} onChange={(e) => editForm.setData('payment_method', e.target.value)}>
                                {paymentMethods.map((m) => <option key={m.value} value={m.value}>{m.label}</option>)}
                            </select>
                        </div>
                        <div>
                            <Label>Payment Date</Label>
                            <Input type="datetime-local" value={editForm.data.payment_date} onChange={(e) => editForm.setData('payment_date', e.target.value)} required />
                        </div>
                        <div>
                            <Label>Remarks</Label>
                            <Textarea value={editForm.data.remarks} onChange={(e) => editForm.setData('remarks', e.target.value)} />
                        </div>
                        <Button type="submit" disabled={editForm.processing}>
                            {editForm.processing && <Spinner />} Update Payment
                        </Button>
                    </form>
                </DialogContent>
            </Dialog>
        </>
    );
}

PaymentShow.layout = (props: Props) => ({
    breadcrumbs: [
        { title: 'Payments', href: index() },
        { title: props.student.full_name, href: show(props.student.id) },
    ],
});
