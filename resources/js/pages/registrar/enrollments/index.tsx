import { Head, Link, router, useForm } from '@inertiajs/react';
import { Plus, Search } from 'lucide-react';
import { FormEvent, useEffect, useState } from 'react';
import InputError from '@/components/input-error';
import RequiredLabel from '@/components/required-label';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { create, index, store } from '@/routes/registrar/students';
import { index as enrollmentsIndex, store as storeEnrollment } from '@/routes/registrar/enrollments';
import type { SelectOption } from '@/types';

type Subject = { id: number; code: string; name: string; units: number };

type Props = {
    students: { id: number; name: string; student_number: string | null }[];
    programs: { id: number; name: string }[];
    sections: { id: number; name: string; program_id: number; year_level: number; remaining_slots: number }[];
    academicYears: { id: number; label: string }[];
    semesters: { id: number; name: string }[];
    enrollmentStatuses: SelectOption[];
    yearLevels: SelectOption[];
    curriculumSubjects: Subject[];
    filters: { search?: string };
};

const selectClass = 'border-input flex h-9 w-full rounded-md border px-3 py-1 text-sm';

export default function EnrollmentsIndex({
    students,
    programs,
    sections,
    academicYears,
    semesters,
    enrollmentStatuses,
    yearLevels,
    curriculumSubjects,
    filters,
}: Props) {
    const [search, setSearch] = useState(filters.search ?? '');
    const { data, setData, post, processing, errors } = useForm({
        user_id: '',
        academic_year_id: '',
        semester_id: '',
        program_id: '',
        year_level: '1',
        section_id: '',
        status: 'enrolled',
        enrolled_at: new Date().toISOString().slice(0, 10),
        remarks: '',
        subject_ids: [] as number[],
    });

    useEffect(() => {
        if (data.program_id && data.year_level && data.semester_id) {
            router.get(
                enrollmentsIndex().url,
                {
                    program_id: data.program_id,
                    year_level: data.year_level,
                    semester_id: data.semester_id,
                    search: filters.search,
                },
                { preserveState: true, only: ['curriculumSubjects'] },
            );
        }
    }, [data.program_id, data.year_level, data.semester_id]);

    useEffect(() => {
        setData(
            'subject_ids',
            curriculumSubjects.map((subject) => subject.id),
        );
    }, [curriculumSubjects]);

    const filteredSections = sections.filter(
        (section) =>
            (!data.program_id || section.program_id === Number(data.program_id)) &&
            (!data.year_level || section.year_level === Number(data.year_level)),
    );

    const toggleSubject = (id: number) => {
        setData(
            'subject_ids',
            data.subject_ids.includes(id)
                ? data.subject_ids.filter((subjectId) => subjectId !== id)
                : [...data.subject_ids, id],
        );
    };

    const searchStudents = (event: FormEvent) => {
        event.preventDefault();
        router.get(enrollmentsIndex().url, { search }, { preserveState: true });
    };

    return (
        <>
            <Head title="Manual Enrollment" />
            <div className="mb-4 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 className="text-lg font-semibold">Manual Enrollment</h2>
                    <p className="text-sm text-muted-foreground">
                        Enroll walk-in students or complete pending enrollments.
                    </p>
                </div>
                <Button variant="outline" asChild>
                    <Link href={create()}>
                        <Plus className="h-4 w-4" /> New Student Profile
                    </Link>
                </Button>
            </div>

            <form onSubmit={searchStudents} className="mb-4 flex gap-2">
                <div className="relative flex-1">
                    <Search className="absolute top-2.5 left-2 h-4 w-4 text-muted-foreground" />
                    <Input
                        value={search}
                        onChange={(e) => setSearch(e.target.value)}
                        placeholder="Search students..."
                        className="pl-8"
                    />
                </div>
                <Button type="submit" variant="secondary">
                    Search
                </Button>
            </form>

            <form
                onSubmit={(e) => {
                    e.preventDefault();
                    post(storeEnrollment().url);
                }}
                className="grid max-w-3xl gap-4 sm:grid-cols-2"
            >
                <div className="sm:col-span-2">
                    <RequiredLabel htmlFor="user_id">Student</RequiredLabel>
                    <select
                        id="user_id"
                        className={selectClass}
                        value={data.user_id}
                        onChange={(e) => setData('user_id', e.target.value)}
                        required
                    >
                        <option value="">Select student</option>
                        {students.map((s) => (
                            <option key={s.id} value={s.id}>
                                {s.student_number} - {s.name}
                            </option>
                        ))}
                    </select>
                    <InputError message={errors.user_id} />
                </div>
                <div>
                    <RequiredLabel htmlFor="academic_year_id">Academic Year</RequiredLabel>
                    <select
                        id="academic_year_id"
                        className={selectClass}
                        value={data.academic_year_id}
                        onChange={(e) => setData('academic_year_id', e.target.value)}
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
                    <RequiredLabel htmlFor="semester_id">Semester</RequiredLabel>
                    <select
                        id="semester_id"
                        className={selectClass}
                        value={data.semester_id}
                        onChange={(e) => setData('semester_id', e.target.value)}
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
                    <RequiredLabel htmlFor="program_id">Program</RequiredLabel>
                    <select
                        id="program_id"
                        className={selectClass}
                        value={data.program_id}
                        onChange={(e) => setData('program_id', e.target.value)}
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
                    <RequiredLabel htmlFor="year_level">Year Level</RequiredLabel>
                    <select
                        id="year_level"
                        className={selectClass}
                        value={data.year_level}
                        onChange={(e) => setData('year_level', e.target.value)}
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
                    <Label htmlFor="section_id">Section</Label>
                    <select
                        id="section_id"
                        className={selectClass}
                        value={data.section_id}
                        onChange={(e) => setData('section_id', e.target.value)}
                    >
                        <option value="">None</option>
                        {filteredSections.map((s) => (
                            <option key={s.id} value={s.id}>
                                {s.name} ({s.remaining_slots} slots)
                            </option>
                        ))}
                    </select>
                    <InputError message={errors.section_id} />
                </div>
                <div>
                    <RequiredLabel htmlFor="status">Enrollment Status</RequiredLabel>
                    <select
                        id="status"
                        className={selectClass}
                        value={data.status}
                        onChange={(e) => setData('status', e.target.value)}
                        required
                    >
                        {enrollmentStatuses.map((s) => (
                            <option key={s.value} value={s.value}>
                                {s.label}
                            </option>
                        ))}
                    </select>
                </div>
                <div>
                    <Label htmlFor="enrolled_at">Enrollment Date</Label>
                    <Input
                        id="enrolled_at"
                        type="date"
                        value={data.enrolled_at}
                        onChange={(e) => setData('enrolled_at', e.target.value)}
                    />
                </div>

                {curriculumSubjects.length > 0 && (
                    <div className="sm:col-span-2 rounded-lg border p-4">
                        <p className="mb-3 text-sm font-medium">Curriculum Subjects</p>
                        <div className="grid gap-2 sm:grid-cols-2">
                            {curriculumSubjects.map((subject) => (
                                <label
                                    key={subject.id}
                                    className="flex items-center gap-2 text-sm"
                                >
                                    <input
                                        type="checkbox"
                                        checked={data.subject_ids.includes(subject.id)}
                                        onChange={() => toggleSubject(subject.id)}
                                    />
                                    {subject.code} - {subject.name} ({subject.units}u)
                                </label>
                            ))}
                        </div>
                    </div>
                )}

                <div className="sm:col-span-2">
                    <Button type="submit" disabled={processing}>
                        {processing && <Spinner />} Save Enrollment
                    </Button>
                </div>
            </form>
        </>
    );
}

EnrollmentsIndex.layout = {
    breadcrumbs: [{ title: 'Manual Enrollment', href: enrollmentsIndex() }],
};
