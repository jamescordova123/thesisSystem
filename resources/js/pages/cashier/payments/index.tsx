import { Head, Link, router } from '@inertiajs/react';
import { Search } from 'lucide-react';
import { FormEvent, useState } from 'react';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { index, show } from '@/routes/cashier/payments';

type Student = {
    id: number;
    student_number: string | null;
    full_name: string;
    program: string | null;
    year_level: number | null;
    section: string | null;
    total_assessment: number;
    total_payments: number;
    remaining_balance: number;
    payment_status: string;
    payment_status_label: string;
    payment_status_color: string;
};

type Props = {
    students: { data: Student[]; links: { url: string | null; label: string; active: boolean }[] };
    filters: Record<string, string | undefined>;
    programs: { id: number; name: string }[];
    sections: { id: number; name: string }[];
    yearLevels: { value: number; label: string }[];
};

const selectClass = 'border-input flex h-9 w-full rounded-md border px-3 py-1 text-sm';
const formatCurrency = (v: number) =>
    new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' }).format(v);

function StatusBadge({ status, label }: { status: string; label: string }) {
    const variant =
        status === 'fully_paid' ? 'default' : status === 'partial' ? 'secondary' : 'destructive';
    return <Badge variant={variant}>{label}</Badge>;
}

export default function PaymentsIndex({ students, filters, programs, sections, yearLevels }: Props) {
    const [search, setSearch] = useState(filters.search ?? '');
    const [programId, setProgramId] = useState(filters.program_id ?? '');
    const [yearLevel, setYearLevel] = useState(filters.year_level ?? '');
    const [sectionId, setSectionId] = useState(filters.section_id ?? '');

    const applyFilters = (e?: FormEvent) => {
        e?.preventDefault();
        router.get(index().url, {
            search,
            program_id: programId || undefined,
            year_level: yearLevel || undefined,
            section_id: sectionId || undefined,
        }, { preserveState: true });
    };

    return (
        <>
            <Head title="Student Payments" />
            <h2 className="mb-4 text-lg font-semibold">Manage Student Payments</h2>

            <form onSubmit={applyFilters} className="mb-4 grid gap-3 rounded-lg border p-4 sm:grid-cols-2 lg:grid-cols-3">
                <div className="relative sm:col-span-2 lg:col-span-3">
                    <Search className="absolute top-2.5 left-2 h-4 w-4 text-muted-foreground" />
                    <Input value={search} onChange={(e) => setSearch(e.target.value)} placeholder="Search by ID or name..." className="pl-8" />
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
                <Button type="submit" variant="secondary">Apply Filters</Button>
            </form>

            <div className="overflow-x-auto rounded-lg border">
                <table className="w-full text-sm">
                    <thead className="bg-muted/50">
                        <tr>
                            <th className="px-4 py-3 text-left">Student ID</th>
                            <th className="px-4 py-3 text-left">Name</th>
                            <th className="px-4 py-3 text-left">Program</th>
                            <th className="px-4 py-3 text-left">Balance</th>
                            <th className="px-4 py-3 text-left">Status</th>
                            <th className="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        {students.data.map((student) => (
                            <tr key={student.id} className="border-t">
                                <td className="px-4 py-3 font-mono text-xs">{student.student_number ?? '—'}</td>
                                <td className="px-4 py-3">{student.full_name}</td>
                                <td className="px-4 py-3">{student.program ?? '—'}</td>
                                <td className="px-4 py-3">{formatCurrency(student.remaining_balance)}</td>
                                <td className="px-4 py-3">
                                    <StatusBadge status={student.payment_status} label={student.payment_status_label} />
                                </td>
                                <td className="px-4 py-3 text-right">
                                    <Button variant="ghost" size="sm" asChild>
                                        <Link href={show(student.id)}>View / Pay</Link>
                                    </Button>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
        </>
    );
}

PaymentsIndex.layout = { breadcrumbs: [{ title: 'Payments', href: index() }] };
