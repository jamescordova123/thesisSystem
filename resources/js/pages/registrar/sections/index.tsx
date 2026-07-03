import { Head, router, useForm } from '@inertiajs/react';
import { Plus, Users } from 'lucide-react';
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
import {
    assignStudent,
    destroy,
    index,
    removeStudent,
    store,
    transferStudent,
} from '@/routes/registrar/sections';

type SectionStudent = {
    id: number;
    name: string;
    student_number: string | null;
};

type Section = {
    id: number;
    name: string;
    program: string;
    year_level: number;
    semester: string;
    academic_year: string;
    max_capacity: number;
    students_count: number;
    remaining_slots: number;
    students: SectionStudent[];
};

type Props = {
    sections: { data: Section[] };
    programs: { id: number; name: string }[];
    semesters: { id: number; name: string }[];
    academicYears: { id: number; label: string }[];
    yearLevels: { value: number; label: string }[];
    students: { id: number; name: string; student_number: string | null }[];
};

const selectClass =
    'border-input flex h-9 w-full rounded-md border px-3 py-1 text-sm';

function ManageSectionDialog({
    section,
    allStudents,
    allSections,
}: {
    section: Section;
    allStudents: Props['students'];
    allSections: Section[];
}) {
    const [open, setOpen] = useState(false);
    const assignForm = useForm({ user_id: '' });
    const transferForm = useForm({ user_id: '', target_section_id: '' });

    const assignedIds = new Set(section.students.map((s) => s.id));
    const availableStudents = allStudents.filter((s) => !assignedIds.has(s.id));
    const otherSections = allSections.filter((s) => s.id !== section.id);

    const handleAssign = (e: React.FormEvent) => {
        e.preventDefault();
        assignForm.post(assignStudent(section.id).url, {
            preserveScroll: true,
            onSuccess: () => assignForm.reset(),
        });
    };

    const handleTransfer = (e: React.FormEvent) => {
        e.preventDefault();
        transferForm.post(transferStudent(section.id).url, {
            preserveScroll: true,
            onSuccess: () => transferForm.reset(),
        });
    };

    return (
        <Dialog open={open} onOpenChange={setOpen}>
            <DialogTrigger asChild>
                <Button variant="outline" size="sm">
                    <Users className="h-4 w-4" /> Manage
                </Button>
            </DialogTrigger>
            <DialogContent className="max-h-[90vh] overflow-y-auto sm:max-w-lg">
                <DialogHeader>
                    <DialogTitle>{section.name} — Students</DialogTitle>
                </DialogHeader>

                <p className="text-sm text-muted-foreground">
                    {section.students_count}/{section.max_capacity} enrolled ·{' '}
                    {section.remaining_slots} slots remaining
                </p>

                {section.students.length > 0 ? (
                    <ul className="space-y-2 rounded-md border p-3">
                        {section.students.map((student) => (
                            <li
                                key={student.id}
                                className="flex items-center justify-between gap-2 text-sm"
                            >
                                <span>
                                    <span className="font-mono text-xs text-muted-foreground">
                                        {student.student_number ?? '—'}
                                    </span>{' '}
                                    {student.name}
                                </span>
                                <Button
                                    variant="ghost"
                                    size="sm"
                                    onClick={() =>
                                        router.delete(
                                            removeStudent({
                                                section: section.id,
                                                user: student.id,
                                            }).url,
                                            { preserveScroll: true },
                                        )
                                    }
                                >
                                    Remove
                                </Button>
                            </li>
                        ))}
                    </ul>
                ) : (
                    <p className="text-sm text-muted-foreground">
                        No students assigned yet.
                    </p>
                )}

                {section.remaining_slots > 0 && (
                    <form onSubmit={handleAssign} className="space-y-2 border-t pt-4">
                        <Label>Add Student</Label>
                        <select
                            className={selectClass}
                            value={assignForm.data.user_id}
                            onChange={(e) =>
                                assignForm.setData('user_id', e.target.value)
                            }
                            required
                        >
                            <option value="">Select student</option>
                            {availableStudents.map((s) => (
                                <option key={s.id} value={s.id}>
                                    {s.student_number} — {s.name}
                                </option>
                            ))}
                        </select>
                        <InputError message={assignForm.errors.user_id} />
                        <Button
                            type="submit"
                            size="sm"
                            disabled={assignForm.processing}
                        >
                            {assignForm.processing && <Spinner />} Assign
                        </Button>
                    </form>
                )}

                {section.students.length > 0 && otherSections.length > 0 && (
                    <form
                        onSubmit={handleTransfer}
                        className="space-y-2 border-t pt-4"
                    >
                        <Label>Transfer Student</Label>
                        <select
                            className={selectClass}
                            value={transferForm.data.user_id}
                            onChange={(e) =>
                                transferForm.setData('user_id', e.target.value)
                            }
                            required
                        >
                            <option value="">From this section</option>
                            {section.students.map((s) => (
                                <option key={s.id} value={s.id}>
                                    {s.student_number} — {s.name}
                                </option>
                            ))}
                        </select>
                        <select
                            className={selectClass}
                            value={transferForm.data.target_section_id}
                            onChange={(e) =>
                                transferForm.setData(
                                    'target_section_id',
                                    e.target.value,
                                )
                            }
                            required
                        >
                            <option value="">To section</option>
                            {otherSections.map((s) => (
                                <option key={s.id} value={s.id}>
                                    {s.name} ({s.remaining_slots} slots)
                                </option>
                            ))}
                        </select>
                        <InputError
                            message={
                                transferForm.errors.user_id ??
                                transferForm.errors.target_section_id
                            }
                        />
                        <Button
                            type="submit"
                            size="sm"
                            variant="secondary"
                            disabled={transferForm.processing}
                        >
                            {transferForm.processing && <Spinner />} Transfer
                        </Button>
                    </form>
                )}
            </DialogContent>
        </Dialog>
    );
}

export default function SectionsIndex({
    sections,
    programs,
    semesters,
    academicYears,
    yearLevels,
    students,
}: Props) {
    const [open, setOpen] = useState(false);
    const { data, setData, post, processing, reset } = useForm({
        name: '',
        program_id: '',
        year_level: '1',
        semester_id: '',
        academic_year_id: '',
        max_capacity: '40',
    });

    return (
        <>
            <Head title="Sections" />
            <div className="mb-4 flex items-center justify-between">
                <h2 className="text-lg font-semibold">Section Management</h2>
                <Dialog open={open} onOpenChange={setOpen}>
                    <DialogTrigger asChild>
                        <Button>
                            <Plus className="h-4 w-4" /> New Section
                        </Button>
                    </DialogTrigger>
                    <DialogContent>
                        <DialogHeader>
                            <DialogTitle>Create Section</DialogTitle>
                        </DialogHeader>
                        <form
                            onSubmit={(e) => {
                                e.preventDefault();
                                post(store().url, {
                                    onSuccess: () => {
                                        setOpen(false);
                                        reset();
                                    },
                                });
                            }}
                            className="grid gap-3"
                        >
                            <div>
                                <Label>Name</Label>
                                <Input
                                    value={data.name}
                                    onChange={(e) =>
                                        setData('name', e.target.value)
                                    }
                                    required
                                />
                            </div>
                            <div>
                                <Label>Program</Label>
                                <select
                                    className={selectClass}
                                    value={data.program_id}
                                    onChange={(e) =>
                                        setData('program_id', e.target.value)
                                    }
                                    required
                                >
                                    <option value="">Select</option>
                                    {programs.map((p) => (
                                        <option key={p.id} value={p.id}>
                                            {p.name}
                                        </option>
                                    ))}
                                </select>
                            </div>
                            <div>
                                <Label>Year Level</Label>
                                <select
                                    className={selectClass}
                                    value={data.year_level}
                                    onChange={(e) =>
                                        setData('year_level', e.target.value)
                                    }
                                    required
                                >
                                    {yearLevels.map((y) => (
                                        <option key={y.value} value={y.value}>
                                            {y.label}
                                        </option>
                                    ))}
                                </select>
                            </div>
                            <div>
                                <Label>Semester</Label>
                                <select
                                    className={selectClass}
                                    value={data.semester_id}
                                    onChange={(e) =>
                                        setData('semester_id', e.target.value)
                                    }
                                    required
                                >
                                    <option value="">Select</option>
                                    {semesters.map((s) => (
                                        <option key={s.id} value={s.id}>
                                            {s.name}
                                        </option>
                                    ))}
                                </select>
                            </div>
                            <div>
                                <Label>Academic Year</Label>
                                <select
                                    className={selectClass}
                                    value={data.academic_year_id}
                                    onChange={(e) =>
                                        setData(
                                            'academic_year_id',
                                            e.target.value,
                                        )
                                    }
                                    required
                                >
                                    <option value="">Select</option>
                                    {academicYears.map((a) => (
                                        <option key={a.id} value={a.id}>
                                            {a.label}
                                        </option>
                                    ))}
                                </select>
                            </div>
                            <div>
                                <Label>Max Capacity</Label>
                                <Input
                                    type="number"
                                    value={data.max_capacity}
                                    onChange={(e) =>
                                        setData('max_capacity', e.target.value)
                                    }
                                    required
                                />
                            </div>
                            <Button type="submit" disabled={processing}>
                                {processing && <Spinner />} Create
                            </Button>
                        </form>
                    </DialogContent>
                </Dialog>
            </div>

            <div className="overflow-x-auto rounded-lg border">
                <table className="w-full text-sm">
                    <thead className="bg-muted/50">
                        <tr>
                            <th className="px-4 py-3 text-left">Section</th>
                            <th className="px-4 py-3 text-left">Program</th>
                            <th className="px-4 py-3 text-left">Year</th>
                            <th className="px-4 py-3 text-left">Semester</th>
                            <th className="px-4 py-3 text-left">Students</th>
                            <th className="px-4 py-3 text-left">Slots Left</th>
                            <th className="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        {sections.data.map((s) => (
                            <tr key={s.id} className="border-t">
                                <td className="px-4 py-3 font-medium">
                                    {s.name}
                                </td>
                                <td className="px-4 py-3">{s.program}</td>
                                <td className="px-4 py-3">
                                    Year {s.year_level}
                                </td>
                                <td className="px-4 py-3">{s.semester}</td>
                                <td className="px-4 py-3">
                                    {s.students_count}/{s.max_capacity}
                                </td>
                                <td className="px-4 py-3">
                                    <Badge
                                        variant={
                                            s.remaining_slots > 0
                                                ? 'secondary'
                                                : 'destructive'
                                        }
                                    >
                                        {s.remaining_slots}
                                    </Badge>
                                </td>
                                <td className="px-4 py-3 text-right">
                                    <div className="flex justify-end gap-2">
                                        <ManageSectionDialog
                                            section={s}
                                            allStudents={students}
                                            allSections={sections.data}
                                        />
                                        <Button
                                            variant="ghost"
                                            size="sm"
                                            onClick={() => {
                                                if (
                                                    confirm(
                                                        'Archive this section?',
                                                    )
                                                ) {
                                                    router.delete(
                                                        destroy(s.id).url,
                                                    );
                                                }
                                            }}
                                        >
                                            Archive
                                        </Button>
                                    </div>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
        </>
    );
}

SectionsIndex.layout = {
    breadcrumbs: [{ title: 'Sections', href: index() }],
};
