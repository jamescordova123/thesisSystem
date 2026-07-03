import { Head, useForm } from '@inertiajs/react';
import InputError from '@/components/input-error';
import RequiredLabel from '@/components/required-label';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { Textarea } from '@/components/ui/textarea';
import { create, index, store } from '@/routes/registrar/students';
import type { SelectOption } from '@/types';

type Props = {
    programs: { id: number; name: string }[];
    sections: { id: number; name: string }[];
    enrollmentStatuses: SelectOption[];
    yearLevels: SelectOption[];
};

const selectClass =
    'border-input flex h-9 w-full rounded-md border bg-transparent px-3 py-1 text-sm shadow-xs';

export default function StudentCreate({
    programs,
    sections,
    enrollmentStatuses,
    yearLevels,
}: Props) {
    const { data, setData, post, processing, errors } = useForm({
        full_name: '',
        email: '',
        password: '',
        password_confirmation: '',
        school_year: '2025-2026',
        program_id: '',
        section_id: '',
        year_level: '1',
        enrollment_status: 'not_enrolled',
        status: 'regular',
        birthdate: '',
        sex: 'male',
        age: '',
        place_of_birth: '',
        current_address: '',
        permanent_address: '',
        guardian_name: '',
        primary_contact_number: '',
    });

    return (
        <>
            <Head title="Add Student" />
            <h2 className="mb-4 text-lg font-semibold">Add New Student</h2>

            <form
                onSubmit={(e) => {
                    e.preventDefault();
                    post(store().url);
                }}
                className="grid max-w-3xl gap-4 sm:grid-cols-2"
            >
                <div className="sm:col-span-2">
                    <RequiredLabel htmlFor="full_name">Full Name</RequiredLabel>
                    <Input id="full_name" value={data.full_name} onChange={(e) => setData('full_name', e.target.value)} required />
                    <InputError message={errors.full_name} />
                </div>
                <div>
                    <RequiredLabel htmlFor="email">Email</RequiredLabel>
                    <Input id="email" type="email" value={data.email} onChange={(e) => setData('email', e.target.value)} required />
                    <InputError message={errors.email} />
                </div>
                <div>
                    <RequiredLabel htmlFor="school_year">School Year</RequiredLabel>
                    <Input id="school_year" value={data.school_year} onChange={(e) => setData('school_year', e.target.value)} required />
                    <InputError message={errors.school_year} />
                </div>
                <div>
                    <RequiredLabel htmlFor="password">Password</RequiredLabel>
                    <Input id="password" type="password" value={data.password} onChange={(e) => setData('password', e.target.value)} required />
                    <InputError message={errors.password} />
                </div>
                <div>
                    <RequiredLabel htmlFor="password_confirmation">Confirm Password</RequiredLabel>
                    <Input id="password_confirmation" type="password" value={data.password_confirmation} onChange={(e) => setData('password_confirmation', e.target.value)} required />
                </div>
                <div>
                    <RequiredLabel htmlFor="program_id">Program</RequiredLabel>
                    <select id="program_id" className={selectClass} value={data.program_id} onChange={(e) => setData('program_id', e.target.value)} required>
                        <option value="">Select program</option>
                        {programs.map((p) => <option key={p.id} value={p.id}>{p.name}</option>)}
                    </select>
                    <InputError message={errors.program_id} />
                </div>
                <div>
                    <RequiredLabel htmlFor="year_level">Year Level</RequiredLabel>
                    <select id="year_level" className={selectClass} value={data.year_level} onChange={(e) => setData('year_level', e.target.value)} required>
                        {yearLevels.map((y) => <option key={y.value} value={y.value}>{y.label}</option>)}
                    </select>
                </div>
                <div>
                    <Label htmlFor="section_id">Section</Label>
                    <select id="section_id" className={selectClass} value={data.section_id} onChange={(e) => setData('section_id', e.target.value)}>
                        <option value="">None</option>
                        {sections.map((s) => <option key={s.id} value={s.id}>{s.name}</option>)}
                    </select>
                </div>
                <div>
                    <RequiredLabel htmlFor="enrollment_status">Enrollment Status</RequiredLabel>
                    <select id="enrollment_status" className={selectClass} value={data.enrollment_status} onChange={(e) => setData('enrollment_status', e.target.value)} required>
                        {enrollmentStatuses.map((s) => <option key={s.value} value={s.value}>{s.label}</option>)}
                    </select>
                </div>
                <div>
                    <RequiredLabel htmlFor="birthdate">Birthdate</RequiredLabel>
                    <Input id="birthdate" type="date" value={data.birthdate} onChange={(e) => setData('birthdate', e.target.value)} required />
                    <InputError message={errors.birthdate} />
                </div>
                <div>
                    <RequiredLabel htmlFor="sex">Sex</RequiredLabel>
                    <select id="sex" className={selectClass} value={data.sex} onChange={(e) => setData('sex', e.target.value)} required>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>
                </div>
                <div>
                    <RequiredLabel htmlFor="age">Age</RequiredLabel>
                    <Input id="age" type="number" value={data.age} onChange={(e) => setData('age', e.target.value)} required />
                    <InputError message={errors.age} />
                </div>
                <div>
                    <RequiredLabel htmlFor="place_of_birth">Place of Birth</RequiredLabel>
                    <Input id="place_of_birth" value={data.place_of_birth} onChange={(e) => setData('place_of_birth', e.target.value)} required />
                </div>
                <div className="sm:col-span-2">
                    <RequiredLabel htmlFor="current_address">Current Address</RequiredLabel>
                    <Textarea id="current_address" value={data.current_address} onChange={(e) => setData('current_address', e.target.value)} required />
                </div>
                <div className="sm:col-span-2">
                    <RequiredLabel htmlFor="permanent_address">Permanent Address</RequiredLabel>
                    <Textarea id="permanent_address" value={data.permanent_address} onChange={(e) => setData('permanent_address', e.target.value)} required />
                </div>
                <div>
                    <RequiredLabel htmlFor="guardian_name">Guardian Name</RequiredLabel>
                    <Input id="guardian_name" value={data.guardian_name} onChange={(e) => setData('guardian_name', e.target.value)} required />
                </div>
                <div>
                    <RequiredLabel htmlFor="primary_contact_number">Contact Number</RequiredLabel>
                    <Input id="primary_contact_number" value={data.primary_contact_number} onChange={(e) => setData('primary_contact_number', e.target.value)} required />
                </div>
                <div className="sm:col-span-2 flex justify-end">
                    <Button type="submit" disabled={processing}>
                        {processing && <Spinner />} Save Student
                    </Button>
                </div>
            </form>
        </>
    );
}

StudentCreate.layout = {
    breadcrumbs: [
        { title: 'Students', href: index() },
        { title: 'Add', href: create() },
    ],
};
