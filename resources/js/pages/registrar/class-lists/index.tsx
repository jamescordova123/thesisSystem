import { Head, Link } from '@inertiajs/react';
import { index, show } from '@/routes/registrar/class-lists';

type Props = { sections: { id: number; name: string; program: string; year_level: number; semester: string; academic_year: string; students_count: number }[] };

export default function ClassListsIndex({ sections }: Props) {
    return (
        <>
            <Head title="Class Lists" />
            <h2 className="mb-4 text-lg font-semibold">Official Class Lists</h2>
            <div className="space-y-2">
                {sections.map((s) => (
                    <Link key={s.id} href={show(s.id)} className="flex items-center justify-between rounded-lg border p-4 hover:bg-muted/50">
                        <div>
                            <p className="font-medium">{s.name}</p>
                            <p className="text-sm text-muted-foreground">{s.program} · Year {s.year_level} · {s.semester} · {s.academic_year}</p>
                        </div>
                        <span className="text-sm">{s.students_count} students</span>
                    </Link>
                ))}
                {sections.length === 0 && <p className="text-muted-foreground">No sections found.</p>}
            </div>
        </>
    );
}

ClassListsIndex.layout = { breadcrumbs: [{ title: 'Class Lists', href: index() }] };
