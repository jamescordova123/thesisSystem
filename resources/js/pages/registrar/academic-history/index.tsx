import { Head, Link, router } from '@inertiajs/react';
import { FormEvent, useState } from 'react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { show } from '@/routes/registrar/academic-history';
import { index } from '@/routes/registrar/academic-history';

type Props = {
    students: { id: number; student_number: string | null; name: string; program: string | null }[];
    search: string;
};

export default function AcademicHistoryIndex({ students, search }: Props) {
    const [term, setTerm] = useState(search);

    return (
        <>
            <Head title="Academic History" />
            <h2 className="mb-4 text-lg font-semibold">Student Academic History</h2>
            <form onSubmit={(e: FormEvent) => { e.preventDefault(); router.get(index().url, { search: term }); }} className="mb-4 flex gap-2">
                <Input value={term} onChange={(e) => setTerm(e.target.value)} placeholder="Student ID or name" />
                <Button type="submit">Search</Button>
            </form>
            <div className="space-y-2">
                {students.map((s) => (
                    <Link key={s.id} href={show(s.id)} className="block rounded-lg border p-3 hover:bg-muted/50">
                        <span className="font-medium">{s.name}</span>
                        <span className="ml-2 text-sm text-muted-foreground">{s.student_number} · {s.program}</span>
                    </Link>
                ))}
            </div>
        </>
    );
}

AcademicHistoryIndex.layout = { breadcrumbs: [{ title: 'Academic History', href: index() }] };
