import { Head, Link } from '@inertiajs/react';
import { show } from '@/routes/registrar/records';
import { index } from '@/routes/registrar/records';

type Props = {
    students: { data: { id: number; student_number: string | null; name: string; program: string | null; records_count: number }[] };
};

export default function RecordsIndex({ students }: Props) {
    return (
        <>
            <Head title="Student Records" />
            <h2 className="mb-4 text-lg font-semibold">Manage Student Records</h2>
            <div className="space-y-2">
                {students.data.map((s) => (
                    <Link key={s.id} href={show(s.id)} className="flex justify-between rounded-lg border p-3 hover:bg-muted/50">
                        <span>{s.student_number} - {s.name}</span>
                        <span className="text-sm text-muted-foreground">{s.records_count} records</span>
                    </Link>
                ))}
            </div>
        </>
    );
}

RecordsIndex.layout = { breadcrumbs: [{ title: 'Records', href: index() }] };
