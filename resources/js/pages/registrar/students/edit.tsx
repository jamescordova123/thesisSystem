import { Head, router, useForm } from '@inertiajs/react';
import InputError from '@/components/input-error';
import RequiredLabel from '@/components/required-label';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { Textarea } from '@/components/ui/textarea';
import { archive, edit, index, update } from '@/routes/registrar/students';
import type { SelectOption } from '@/types';

type Student = {
    id: number;
    email: string;
    full_name: string;
    school_year: string;
    program_id: number | null;
    section_id: number | null;
    year_level: number | null;
    enrollment_status: string;
    status: string;
    birthdate: string;
    sex: string;
    age: number;
    place_of_birth: string;
    current_address: string;
    permanent_address: string;
    guardian_name: string;
    primary_contact_number: string;
    father_name: string | null;
    mother_maiden_name: string | null;
};

type Props = {
    student: Student;
    programs: { id: number; name: string }[];
    sections: { id: number; name: string }[];
    academicYears: { id: number; label: string }[];
    enrollmentStatuses: SelectOption[];
    yearLevels: SelectOption[];
};

const selectClass =
    'border-input flex h-9 w-full rounded-md border bg-transparent px-3 py-1 text-sm shadow-xs';

export default function StudentEdit({
    student,
    programs,
    sections,
    academicYears,
    enrollmentStatuses,
    yearLevels,
}: Props) {
    const { data, setData, put, processing, errors } = useForm({
        full_name: student.full_name,
        email: student.email,
        school_year: student.school_year ?? '',
        program_id: String(student.program_id ?? ''),
        section_id: String(student.section_id ?? ''),
        year_level: String(student.year_level ?? '1'),
        enrollment_status: student.enrollment_status,
        status: student.status ?? 'regular',
        birthdate: student.birthdate ?? '',
        sex: student.sex ?? '',
        age: String(student.age ?? ''),
        place_of_birth: student.place_of_birth ?? '',
        current_address: student.current_address ?? '',
        permanent_address: student.permanent_address ?? '',
        guardian_name: student.guardian_name ?? '',
        primary_contact_number: student.primary_contact_number ?? '',
        father_name: student.father_name ?? '',
        mother_maiden_name: student.mother_maiden_name ?? '',
    });

    return (
        <>
            <Head title={`Edit ${student.full_name}`} />
            <div className="mb-4 flex items-center justify-between">
                <h2 className="text-lg font-semibold">Edit Student</h2>
                <Button
                    variant="destructive"
                    size="sm"
                    onClick={() => {
                        if (confirm('Archive this student profile?')) {
                            router.post(archive(student.id).url);
                        }
                    }}
                >
                    Archive
                </Button>
            </div>

            <form
                onSubmit={(e) => {
                    e.preventDefault();
                    put(update(student.id).url);
                }}
                className="grid max-w-3xl gap-4 sm:grid-cols-2"
            >
                <div className="sm:col-span-2">
                    <RequiredLabel htmlFor="full_name">Full Name</RequiredLabel>
                    <Input
                        id="full_name"
                        value={data.full_name}
                        onChange={(e) => setData('full_name', e.target.value)}
                        required
                    />
                    <InputError message={errors.full_name} />
                </div>
                <div>
                    <RequiredLabel htmlFor="email">Email</RequiredLabel>
                    <Input
                        id="email"
                        type="email"
                        value={data.email}
                        onChange={(e) => setData('email', e.target.value)}
                        required
                    />
                    <InputError message={errors.email} />
                </div>
                <div>
                    <RequiredLabel htmlFor="school_year">School Year</RequiredLabel>
                    <Input
                        id="school_year"
                        value={data.school_year}
                        onChange={(e) => setData('school_year', e.target.value)}
                        required
                    />
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
                        {sections.map((s) => (
                            <option key={s.id} value={s.id}>
                                {s.name}
                            </option>
                        ))}
                    </select>
                </div>
                <div>
                    <RequiredLabel htmlFor="enrollment_status">Enrollment Status</RequiredLabel>
                    <select
                        id="enrollment_status"
                        className={selectClass}
                        value={data.enrollment_status}
                        onChange={(e) => setData('enrollment_status', e.target.value)}
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
                    <RequiredLabel htmlFor="status">Academic Status</RequiredLabel>
                    <select
                        id="status"
                        className={selectClass}
                        value={data.status}
                        onChange={(e) => setData('status', e.target.value)}
                        required
                    >
                        <option value="regular">Regular</option>
                        <option value="irregular">Irregular</option>
                    </select>
                </div>
                <div>
                    <RequiredLabel htmlFor="birthdate">Birthdate</RequiredLabel>
                    <Input
                        id="birthdate"
                        type="date"
                        value={data.birthdate}
                        onChange={(e) => setData('birthdate', e.target.value)}
                        required
                    />
                </div>
                <div>
                    <RequiredLabel htmlFor="sex">Sex</RequiredLabel>
                    <select
                        id="sex"
                        className={selectClass}
                        value={data.sex}
                        onChange={(e) => setData('sex', e.target.value)}
                        required
                    >
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>
                </div>
                <div>
                    <RequiredLabel htmlFor="age">Age</RequiredLabel>
                    <Input
                        id="age"
                        type="number"
                        value={data.age}
                        onChange={(e) => setData('age', e.target.value)}
                        required
                    />
                </div>
                <div>
                    <RequiredLabel htmlFor="place_of_birth">Place of Birth</RequiredLabel>
                    <Input
                        id="place_of_birth"
                        value={data.place_of_birth}
                        onChange={(e) => setData('place_of_birth', e.target.value)}
                        required
                    />
                </div>
                <div className="sm:col-span-2">
                    <RequiredLabel htmlFor="current_address">Current Address</RequiredLabel>
                    <Textarea
                        id="current_address"
                        value={data.current_address}
                        onChange={(e) => setData('current_address', e.target.value)}
                        required
                    />
                </div>
                <div className="sm:col-span-2">
                    <RequiredLabel htmlFor="permanent_address">Permanent Address</RequiredLabel>
                    <Textarea
                        id="permanent_address"
                        value={data.permanent_address}
                        onChange={(e) => setData('permanent_address', e.target.value)}
                        required
                    />
                </div>
                <div>
                    <RequiredLabel htmlFor="guardian_name">Guardian Name</RequiredLabel>
                    <Input
                        id="guardian_name"
                        value={data.guardian_name}
                        onChange={(e) => setData('guardian_name', e.target.value)}
                        required
                    />
                </div>
                <div>
                    <RequiredLabel htmlFor="primary_contact_number">Contact Number</RequiredLabel>
                    <Input
                        id="primary_contact_number"
                        value={data.primary_contact_number}
                        onChange={(e) => setData('primary_contact_number', e.target.value)}
                        required
                    />
                </div>
                <div>
                    <Label htmlFor="father_name">Father&apos;s Name</Label>
                    <Input
                        id="father_name"
                        value={data.father_name}
                        onChange={(e) => setData('father_name', e.target.value)}
                    />
                </div>
                <div>
                    <Label htmlFor="mother_maiden_name">Mother&apos;s Maiden Name</Label>
                    <Input
                        id="mother_maiden_name"
                        value={data.mother_maiden_name}
                        onChange={(e) => setData('mother_maiden_name', e.target.value)}
                    />
                </div>
                <div className="flex justify-end sm:col-span-2">
                    <Button type="submit" disabled={processing}>
                        {processing && <Spinner />} Update Student
                    </Button>
                </div>
            </form>
        </>
    );
}

StudentEdit.layout = (props: { student: Student }) => ({
    breadcrumbs: [
        { title: 'Students', href: index() },
        { title: props.student.full_name, href: edit(props.student.id) },
    ],
});
