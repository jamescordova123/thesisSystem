import { Head, Link, router } from '@inertiajs/react';
import { Plus, Search } from 'lucide-react';
import { FormEvent, useState } from 'react';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { create, edit, index } from '@/routes/registrar/students';
import type { SelectOption } from '@/types';

type Student = {
    id: number;
    student_number: string | null;
    full_name: string;
    email: string;
    program: string | null;
    section: string | null;
    year_level: number | null;
    enrollment_status: string;
    is_archived: boolean;
};

type Paginated<T> = {
    data: T[];
    links: { url: string | null; label: string; active: boolean }[];
};

type Props = {
    students: Paginated<Student>;
    filters: Record<string, string | boolean | undefined>;
    programs: { id: number; name: string }[];
    sections: { id: number; name: string }[];
    enrollmentStatuses: SelectOption[];
    yearLevels: SelectOption[];
};

const selectClass =
    'border-input flex h-9 w-full rounded-md border bg-transparent px-3 py-1 text-sm shadow-xs';

export default function StudentsIndex({
    students,
    filters,
    programs,
    sections,
    enrollmentStatuses,
    yearLevels,
}: Props) {
    const [search, setSearch] = useState((filters.search as string) ?? '');
    const [programId, setProgramId] = useState((filters.program_id as string) ?? '');
    const [yearLevel, setYearLevel] = useState((filters.year_level as string) ?? '');
    const [sectionId, setSectionId] = useState((filters.section_id as string) ?? '');
    const [enrollmentStatus, setEnrollmentStatus] = useState(
        (filters.enrollment_status as string) ?? '',
    );

    const applyFilters = (event?: FormEvent) => {
        event?.preventDefault();
        router.get(
            index().url,
            {
                search,
                program_id: programId || undefined,
                year_level: yearLevel || undefined,
                section_id: sectionId || undefined,
                enrollment_status: enrollmentStatus || undefined,
            },
            { preserveState: true },
        );
    };

    return (
        <>
            <Head title="Manage Students" />

            <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 className="text-lg font-semibold">Student Profiles</h2>
                    <p className="text-sm text-muted-foreground">
                        View, add, edit, and manage student profiles.
                    </p>
                </div>
                <Button asChild>
                    <Link href={create()}>
                        <Plus className="h-4 w-4" /> Add Student
                    </Link>
                </Button>
            </div>

            <form onSubmit={applyFilters} className="grid gap-3 rounded-lg border p-4 sm:grid-cols-2 lg:grid-cols-3">
                <div className="relative sm:col-span-2 lg:col-span-3">
                    <Search className="absolute top-2.5 left-2 h-4 w-4 text-muted-foreground" />
                    <Input
                        value={search}
                        onChange={(e) => setSearch(e.target.value)}
                        placeholder="Search by ID, name, or email..."
                        className="pl-8"
                    />
                </div>
                <select
                    className={selectClass}
                    value={programId}
                    onChange={(e) => setProgramId(e.target.value)}
                >
                    <option value="">All programs</option>
                    {programs.map((p) => (
                        <option key={p.id} value={p.id}>
                            {p.name}
                        </option>
                    ))}
                </select>
                <select
                    className={selectClass}
                    value={yearLevel}
                    onChange={(e) => setYearLevel(e.target.value)}
                >
                    <option value="">All year levels</option>
                    {yearLevels.map((y) => (
                        <option key={y.value} value={y.value}>
                            {y.label}
                        </option>
                    ))}
                </select>
                <select
                    className={selectClass}
                    value={sectionId}
                    onChange={(e) => setSectionId(e.target.value)}
                >
                    <option value="">All sections</option>
                    {sections.map((s) => (
                        <option key={s.id} value={s.id}>
                            {s.name}
                        </option>
                    ))}
                </select>
                <select
                    className={selectClass}
                    value={enrollmentStatus}
                    onChange={(e) => setEnrollmentStatus(e.target.value)}
                >
                    <option value="">All statuses</option>
                    {enrollmentStatuses.map((s) => (
                        <option key={s.value} value={s.value}>
                            {s.label}
                        </option>
                    ))}
                </select>
                <Button type="submit" variant="secondary" className="sm:col-span-2 lg:col-span-1">
                    Apply Filters
                </Button>
            </form>

            <div className="overflow-x-auto rounded-lg border">
                <table className="w-full text-sm">
                    <thead className="bg-muted/50">
                        <tr>
                            <th className="px-4 py-3 text-left font-medium">Student ID</th>
                            <th className="px-4 py-3 text-left font-medium">Name</th>
                            <th className="px-4 py-3 text-left font-medium">Program</th>
                            <th className="px-4 py-3 text-left font-medium">Year</th>
                            <th className="px-4 py-3 text-left font-medium">Section</th>
                            <th className="px-4 py-3 text-left font-medium">Status</th>
                            <th className="px-4 py-3 text-right font-medium">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        {students.data.map((student) => (
                            <tr key={student.id} className="border-t">
                                <td className="px-4 py-3 font-mono text-xs">
                                    {student.student_number ?? '—'}
                                </td>
                                <td className="px-4 py-3">{student.full_name}</td>
                                <td className="px-4 py-3">{student.program ?? '—'}</td>
                                <td className="px-4 py-3">
                                    {student.year_level ? `Year ${student.year_level}` : '—'}
                                </td>
                                <td className="px-4 py-3">{student.section ?? '—'}</td>
                                <td className="px-4 py-3">
                                    <Badge variant="secondary" className="capitalize">
                                        {student.enrollment_status.replace('_', ' ')}
                                    </Badge>
                                </td>
                                <td className="px-4 py-3 text-right">
                                    <Button variant="ghost" size="sm" asChild>
                                        <Link href={edit(student.id)}>Edit</Link>
                                    </Button>
                                </td>
                            </tr>
                        ))}
                        {students.data.length === 0 && (
                            <tr>
                                <td colSpan={7} className="px-4 py-8 text-center text-muted-foreground">
                                    No students found.
                                </td>
                            </tr>
                        )}
                    </tbody>
                </table>
            </div>

            {students.links.length > 3 && (
                <div className="flex flex-wrap gap-1">
                    {students.links.map((link, index) => (
                        <Button
                            key={index}
                            variant={link.active ? 'default' : 'outline'}
                            size="sm"
                            disabled={!link.url}
                            asChild={!!link.url}
                        >
                            {link.url ? (
                                <Link href={link.url} preserveScroll preserveState>
                                    <span dangerouslySetInnerHTML={{ __html: link.label }} />
                                </Link>
                            ) : (
                                <span dangerouslySetInnerHTML={{ __html: link.label }} />
                            )}
                        </Button>
                    ))}
                </div>
            )}
        </>
    );
}

StudentsIndex.layout = {
    breadcrumbs: [{ title: 'Students', href: index() }],
};
