import { Head } from '@inertiajs/react';
import { AlertCircle, CheckCircle2, Clock, DollarSign, Users } from 'lucide-react';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { dashboard } from '@/routes/cashier';

type Stats = {
    todayTotal: number;
    semesterTotal: number;
    outstandingStudents: number;
    fullyPaidStudents: number;
};

type Payment = {
    id: number;
    student: string;
    student_number: string | null;
    amount: number;
    official_receipt_number: string;
    payment_method: string;
    cashier: string | null;
    payment_date: string | null;
};

type Props = { stats: Stats; recentPayments: Payment[] };

const formatCurrency = (value: number) =>
    new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' }).format(value);

export default function CashierDashboard({ stats, recentPayments }: Props) {
    const cards = [
        { title: 'Collected Today', value: formatCurrency(stats.todayTotal), icon: DollarSign },
        { title: 'Collected This Month', value: formatCurrency(stats.semesterTotal), icon: Clock },
        { title: 'Outstanding Balances', value: stats.outstandingStudents, icon: AlertCircle },
        { title: 'Fully Paid Students', value: stats.fullyPaidStudents, icon: CheckCircle2 },
    ];

    return (
        <>
            <Head title="Cashier Dashboard" />

            <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                {cards.map((card) => (
                    <Card key={card.title}>
                        <CardHeader className="flex flex-row items-center justify-between pb-2">
                            <CardTitle className="text-sm font-medium text-muted-foreground">
                                {card.title}
                            </CardTitle>
                            <card.icon className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{card.value}</div>
                        </CardContent>
                    </Card>
                ))}
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>Recent Payment Transactions</CardTitle>
                </CardHeader>
                <CardContent className="space-y-3">
                    {recentPayments.length === 0 ? (
                        <p className="text-sm text-muted-foreground">No recent payments.</p>
                    ) : (
                        recentPayments.map((payment) => (
                            <div
                                key={payment.id}
                                className="flex items-center justify-between border-b pb-2 last:border-0"
                            >
                                <div>
                                    <p className="text-sm font-medium">{payment.student}</p>
                                    <p className="text-xs text-muted-foreground">
                                        {payment.student_number} · OR {payment.official_receipt_number} ·{' '}
                                        {payment.payment_method.replace('_', ' ')}
                                    </p>
                                </div>
                                <div className="text-right">
                                    <p className="text-sm font-semibold">
                                        {formatCurrency(payment.amount)}
                                    </p>
                                    <p className="text-xs text-muted-foreground">
                                        {payment.payment_date
                                            ? new Date(payment.payment_date).toLocaleString()
                                            : ''}
                                    </p>
                                </div>
                            </div>
                        ))
                    )}
                </CardContent>
            </Card>
        </>
    );
}

CashierDashboard.layout = {
    breadcrumbs: [{ title: 'Cashier', href: dashboard() }],
};
