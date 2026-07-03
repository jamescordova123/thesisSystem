import { Head, Link, router } from '@inertiajs/react';
import { Search } from 'lucide-react';
import { FormEvent, useState } from 'react';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { search } from '@/routes/cashier';
import { show as ledgerShow } from '@/routes/cashier/ledger';
import { show as paymentShow } from '@/routes/cashier/payments';

type Result = {
    id: number;
    student_number: string | null;
    full_name: string;
    program: string | null;
    year_level: number | null;
    section: string | null;
    remaining_balance: number;
    payment_status: string;
    payment_status_label: string;
};

type Props = {
    results: Result[];
    filters: Record<string, string | undefined>;
    programs: { id: number; name: string }[];
    sections: { id: number; name: string }[];
    yearLevels: { value: number; label: string }[];
};

const selectClass = 'border-input flex h-9 w-full rounded-md border px-3 py-1 text-sm';
const formatCurrency = (v: number) =>
    new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' }).format(v);

export default function CashierSearch({ results, filters, programs, sections, yearLevels }: Props) {
    const [q, setQ] = useState(filters.q ?? '');
    const [programId, setProgramId] = useState(filters.program_id ?? '');
    const [yearLevel, setYearLevel] = useState(filters.year_level ?? '');
    const [sectionId, setSectionId] = useState(filters.section_id ?? '');

    const submit = (e: FormEvent) => {
        e.preventDefault();
        router.get(search().url, {
            q, program_id: programId || undefined,
            year_level: yearLevel || undefined, section_id: sectionId || undefined,
        });
    };

    return (
        <>
            <Head title="Student Search" />
            <h2 className="mb-4 text-lg font-semibold">Student Search</h2>

            <form onSubmit={submit} className="mb-6 grid gap-3 rounded-lg border p-4 sm:grid-cols-2">
                <div className="relative sm:col-span-2">
                    <Search className="absolute top-2.5 left-2 h-4 w-4 text-muted-foreground" />
                    <Input value={q} onChange={(e) => setQ(e.target.value)} placeholder="Student ID or name..." className="pl-8" />
                </div>
                <select className={selectClass} value={programId} onChange={(e) => setProgramId(e.target.value)}>
                    <option value="">All programs</option>
                    {programs.map((p) => <option key={p.id} value={p.id}>{p.name}</option>)}
                </select>
                <select className={selectClass} value={yearLevel} onChange={(e) => setYearLevel(e.target.value)}>
                    <option value="">All year levels</option>
                    {yearLevels.map((y) => <option key={y.value} value={y.value}>{y.label}</option>)}
                </select>
                <select className={selectClass} value={sectionId} onChange={(e) => setSectionId(e.target.value)}>
                    <option value="">All sections</option>
                    {sections.map((s) => <option key={s.id} value={s.id}>{s.name}</option>)}
                </select>
                <Button type="submit">Search</Button>
            </form>

            <div className="space-y-3">
                {results.map((student) => (
                    <div key={student.id} className="flex flex-col gap-3 rounded-lg border p-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p className="font-medium">{student.full_name}</p>
                            <p className="text-sm text-muted-foreground">
                                {student.student_number} · {student.program} · Year {student.year_level} · {student.section}
                            </p>
                            <div className="mt-1 flex gap-2">
                                <Badge variant={student.payment_status === 'fully_paid' ? 'default' : student.payment_status === 'partial' ? 'secondary' : 'destructive'}>
                                    {student.payment_status_label}
                                </Badge>
                                <span className="text-sm">Balance: {formatCurrency(student.remaining_balance)}</span>
                            </div>
                        </div>
                        <div className="flex gap-2">
                            <Button variant="outline" size="sm" asChild><Link href={paymentShow(student.id)}>Payments</Link></Button>
                            <Button variant="ghost" size="sm" asChild><Link href={ledgerShow(student.id)}>Ledger</Link></Button>
                        </div>
                    </div>
                ))}
                {results.length === 0 && <p className="text-center text-muted-foreground py-8">No students found.</p>}
            </div>
        </>
    );
}

CashierSearch.layout = { breadcrumbs: [{ title: 'Search', href: search() }] };
