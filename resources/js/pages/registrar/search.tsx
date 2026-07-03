import { Head, Link, router } from '@inertiajs/react';
import { Search } from 'lucide-react';
import { FormEvent, useState } from 'react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { edit as editStudent } from '@/routes/registrar/students';
import { search } from '@/routes/registrar';
import { show as showClassList } from '@/routes/registrar/class-lists';

type Props = {
    query: string;
    students: { id: number; student_number: string | null; name: string; program: string | null; section: string | null }[];
    sections: { id: number; name: string; program: string; year_level: number }[];
};

export default function RegistrarSearch({ query, students, sections }: Props) {
    const [q, setQ] = useState(query);

    const submit = (e: FormEvent) => {
        e.preventDefault();
        router.get(search().url, { q }, { preserveState: true });
    };

    return (
        <>
            <Head title="Search" />
            <h2 className="mb-4 text-lg font-semibold">Global Search</h2>
            <form onSubmit={submit} className="mb-6 flex gap-2">
                <div className="relative flex-1">
                    <Search className="absolute top-2.5 left-2 h-4 w-4 text-muted-foreground" />
                    <Input value={q} onChange={(e) => setQ(e.target.value)} placeholder="Student ID, name, program, section..." className="pl-8" />
                </div>
                <Button type="submit">Search</Button>
            </form>

            {students.length > 0 && (
                <div className="mb-6">
                    <h3 className="mb-2 font-medium">Students</h3>
                    <div className="space-y-2">
                        {students.map((s) => (
                            <Link key={s.id} href={editStudent(s.id)} className="block rounded-lg border p-3 hover:bg-muted/50">
                                <span className="font-medium">{s.name}</span>
                                <span className="ml-2 text-sm text-muted-foreground">{s.student_number} · {s.program} · {s.section}</span>
                            </Link>
                        ))}
                    </div>
                </div>
            )}

            {sections.length > 0 && (
                <div>
                    <h3 className="mb-2 font-medium">Sections</h3>
                    <div className="space-y-2">
                        {sections.map((s) => (
                            <Link key={s.id} href={showClassList(s.id)} className="block rounded-lg border p-3 hover:bg-muted/50">
                                <span className="font-medium">{s.name}</span>
                                <span className="ml-2 text-sm text-muted-foreground">{s.program} · Year {s.year_level}</span>
                            </Link>
                        ))}
                    </div>
                </div>
            )}
        </>
    );
}

RegistrarSearch.layout = { breadcrumbs: [{ title: 'Search', href: search() }] };
