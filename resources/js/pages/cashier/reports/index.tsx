import { Head } from '@inertiajs/react';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import {
    dailyCollection, fullyPaid, monthlyCollection, noPayment,
    outstandingBalance, partialPayment,
} from '@/routes/cashier/exports';
import { index } from '@/routes/cashier/reports';

type Report = { key: string; label: string; count?: number };

type Props = { reportTypes: Report[] };

const exportRoutes: Record<string, { print?: string; csv: string }> = {
    'daily-collection': { print: dailyCollection().url, csv: `${dailyCollection().url}?format=csv` },
    'monthly-collection': { print: monthlyCollection().url, csv: `${monthlyCollection().url}?format=csv` },
    'outstanding-balance': { csv: outstandingBalance().url },
    'fully-paid': { csv: fullyPaid().url },
    'partial-payment': { csv: partialPayment().url },
    'no-payment': { csv: noPayment().url },
};

export default function ReportsIndex({ reportTypes }: Props) {
    return (
        <>
            <Head title="Cashier Reports" />
            <h2 className="mb-4 text-lg font-semibold">Reports</h2>
            <div className="grid gap-4 sm:grid-cols-2">
                {reportTypes.map((report) => {
                    const exports = exportRoutes[report.key];
                    return (
                        <Card key={report.key}>
                            <CardHeader><CardTitle className="text-base">{report.label}</CardTitle></CardHeader>
                            <CardContent className="space-y-3">
                                {report.count !== undefined && <p className="text-2xl font-bold">{report.count}</p>}
                                {exports && (
                                    <div className="flex flex-wrap gap-2">
                                        {exports.print && (
                                            <Button variant="outline" size="sm" asChild>
                                                <a href={exports.print} target="_blank" rel="noreferrer">Print / PDF</a>
                                            </Button>
                                        )}
                                        <Button variant="secondary" size="sm" asChild>
                                            <a href={exports.csv}>Export Excel</a>
                                        </Button>
                                    </div>
                                )}
                            </CardContent>
                        </Card>
                    );
                })}
            </div>
        </>
    );
}

ReportsIndex.layout = { breadcrumbs: [{ title: 'Reports', href: index() }] };
