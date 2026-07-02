import { Head, useForm, usePage } from '@inertiajs/react';
import InputError from '@/components/input-error';
import RequiredLabel from '@/components/required-label';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { Textarea } from '@/components/ui/textarea';
import { formatDateDisplay } from '@/lib/dates';
import { cn } from '@/lib/utils';
import { edit, store } from '@/routes/information-sheet';
import type {
    SelectOption,
    StudentInformationData,
    StudentInformationFormData,
} from '@/types';

type Props = {
    information: StudentInformationData | null;
    sexOptions: SelectOption[];
    statusOptions: SelectOption[];
};

const selectClassName = cn(
    'border-input flex h-9 w-full rounded-md border bg-transparent px-3 py-1 text-base shadow-xs transition-[color,box-shadow] outline-none focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm',
);

const emptyForm: StudentInformationFormData = {
    school_year: '',
    full_name: '',
    academic_program: '',
    status: '',
    birthdate: '',
    sex: '',
    age: '',
    place_of_birth: '',
    current_address: '',
    permanent_address: '',
    father_name: '',
    mother_maiden_name: '',
    guardian_name: '',
    primary_contact_number: '',
    facebook_account: '',
    facebook_profile: '',
    last_school_attended: '',
    previous_section: '',
    last_school_address: '',
    academic_year: '',
    completion_date: '',
    gwa: '',
};

function toFormData(information: StudentInformationData): StudentInformationFormData {
    return {
        school_year: information.school_year,
        full_name: information.full_name,
        academic_program: information.academic_program,
        status: information.status,
        birthdate: information.birthdate,
        sex: information.sex,
        age: String(information.age),
        place_of_birth: information.place_of_birth,
        current_address: information.current_address,
        permanent_address: information.permanent_address,
        father_name: information.father_name,
        mother_maiden_name: information.mother_maiden_name,
        guardian_name: information.guardian_name,
        primary_contact_number: information.primary_contact_number,
        facebook_account: information.facebook_account,
        facebook_profile: information.facebook_profile,
        last_school_attended: information.last_school_attended,
        previous_section: information.previous_section,
        last_school_address: information.last_school_address,
        academic_year: information.academic_year,
        completion_date: information.completion_date,
        gwa: information.gwa,
    };
}

function FormSection({
    title,
    description,
    children,
}: {
    title: string;
    description?: string;
    children: React.ReactNode;
}) {
    return (
        <section className="space-y-4 rounded-xl border p-4 sm:p-6">
            <div>
                <h2 className="text-base font-semibold">{title}</h2>
                {description ? (
                    <p className="text-sm text-muted-foreground">
                        {description}
                    </p>
                ) : null}
            </div>
            <div className="grid gap-4 sm:grid-cols-2">{children}</div>
        </section>
    );
}

function Field({
    className,
    children,
}: {
    className?: string;
    children: React.ReactNode;
}) {
    return (
        <div className={cn('grid gap-2 sm:col-span-1', className)}>
            {children}
        </div>
    );
}

export default function InformationSheetIndex({
    information,
    sexOptions,
    statusOptions,
}: Props) {
    const page = usePage();
    const teamSlug = page.props.currentTeam?.slug ?? '';
    const isEditing = information !== null;

    const { data, setData, post, processing, errors } = useForm<StudentInformationFormData>(
        information ? toFormData(information) : emptyForm,
    );

    const submit = (event: React.FormEvent) => {
        event.preventDefault();
        post(store(teamSlug).url, { preserveScroll: true });
    };

    return (
        <>
            <Head title="My Information Sheet" />

            <div className="mx-auto flex w-full max-w-4xl flex-col gap-6 p-4">
                <div>
                    <h1 className="text-xl font-semibold tracking-tight">
                        My Information Sheet
                    </h1>
                    <p className="text-sm text-muted-foreground">
                        {isEditing
                            ? 'Review and update your personal and academic information.'
                            : 'Complete your information sheet to get started.'}
                    </p>
                </div>

                <form onSubmit={submit} className="flex flex-col gap-6">
                    <FormSection
                        title="Academic Information"
                        description="Your current enrollment details."
                    >
                        <Field>
                            <RequiredLabel htmlFor="school_year">
                                School Year
                            </RequiredLabel>
                            <Input
                                id="school_year"
                                value={data.school_year}
                                onChange={(e) =>
                                    setData('school_year', e.target.value)
                                }
                                placeholder="e.g. 2024-2025"
                                required
                            />
                            <InputError message={errors.school_year} />
                        </Field>

                        <Field>
                            <RequiredLabel htmlFor="academic_program">
                                Assigned Academic Program
                            </RequiredLabel>
                            <Input
                                id="academic_program"
                                value={data.academic_program}
                                onChange={(e) =>
                                    setData('academic_program', e.target.value)
                                }
                                placeholder="e.g. Bachelor of Science in IT"
                                required
                            />
                            <InputError message={errors.academic_program} />
                        </Field>

                        <Field>
                            <RequiredLabel htmlFor="status">Status</RequiredLabel>
                            <select
                                id="status"
                                value={data.status}
                                onChange={(e) =>
                                    setData('status', e.target.value)
                                }
                                className={selectClassName}
                                required
                            >
                                <option value="">Select status</option>
                                {statusOptions.map((option) => (
                                    <option
                                        key={option.value}
                                        value={option.value}
                                    >
                                        {option.label}
                                    </option>
                                ))}
                            </select>
                            <InputError message={errors.status} />
                        </Field>
                    </FormSection>

                    <FormSection
                        title="Personal Information"
                        description="Basic personal details about you."
                    >
                        <Field className="sm:col-span-2">
                            <RequiredLabel htmlFor="full_name">
                                Full Name of Student
                            </RequiredLabel>
                            <Input
                                id="full_name"
                                value={data.full_name}
                                onChange={(e) =>
                                    setData('full_name', e.target.value)
                                }
                                required
                            />
                            <InputError message={errors.full_name} />
                        </Field>

                        <Field>
                            <RequiredLabel htmlFor="birthdate">
                                Birthdate
                            </RequiredLabel>
                            <Input
                                id="birthdate"
                                type="date"
                                value={data.birthdate}
                                onChange={(e) =>
                                    setData('birthdate', e.target.value)
                                }
                                required
                            />
                            {data.birthdate ? (
                                <p className="text-xs text-muted-foreground">
                                    {formatDateDisplay(data.birthdate)}
                                </p>
                            ) : null}
                            <InputError message={errors.birthdate} />
                        </Field>

                        <Field>
                            <RequiredLabel htmlFor="sex">Sex</RequiredLabel>
                            <select
                                id="sex"
                                value={data.sex}
                                onChange={(e) =>
                                    setData('sex', e.target.value)
                                }
                                className={selectClassName}
                                required
                            >
                                <option value="">Select sex</option>
                                {sexOptions.map((option) => (
                                    <option
                                        key={option.value}
                                        value={option.value}
                                    >
                                        {option.label}
                                    </option>
                                ))}
                            </select>
                            <InputError message={errors.sex} />
                        </Field>

                        <Field>
                            <RequiredLabel htmlFor="age">Age</RequiredLabel>
                            <Input
                                id="age"
                                type="number"
                                min={1}
                                max={120}
                                value={data.age}
                                onChange={(e) =>
                                    setData('age', e.target.value)
                                }
                                required
                            />
                            <InputError message={errors.age} />
                        </Field>

                        <Field>
                            <RequiredLabel htmlFor="place_of_birth">
                                Place of Birth
                            </RequiredLabel>
                            <Input
                                id="place_of_birth"
                                value={data.place_of_birth}
                                onChange={(e) =>
                                    setData('place_of_birth', e.target.value)
                                }
                                required
                            />
                            <InputError message={errors.place_of_birth} />
                        </Field>

                        <Field className="sm:col-span-2">
                            <RequiredLabel htmlFor="current_address">
                                Current Address
                            </RequiredLabel>
                            <Textarea
                                id="current_address"
                                value={data.current_address}
                                onChange={(e) =>
                                    setData('current_address', e.target.value)
                                }
                                rows={3}
                                required
                            />
                            <InputError message={errors.current_address} />
                        </Field>

                        <Field className="sm:col-span-2">
                            <RequiredLabel htmlFor="permanent_address">
                                Permanent Address
                            </RequiredLabel>
                            <Textarea
                                id="permanent_address"
                                value={data.permanent_address}
                                onChange={(e) =>
                                    setData(
                                        'permanent_address',
                                        e.target.value,
                                    )
                                }
                                rows={3}
                                required
                            />
                            <InputError message={errors.permanent_address} />
                        </Field>
                    </FormSection>

                    <FormSection title="Family Information">
                        <Field>
                            <Label htmlFor="father_name">
                                Father&apos;s Full Name
                            </Label>
                            <Input
                                id="father_name"
                                value={data.father_name}
                                onChange={(e) =>
                                    setData('father_name', e.target.value)
                                }
                            />
                            <InputError message={errors.father_name} />
                        </Field>

                        <Field>
                            <Label htmlFor="mother_maiden_name">
                                Mother&apos;s Full Maiden Name
                            </Label>
                            <Input
                                id="mother_maiden_name"
                                value={data.mother_maiden_name}
                                onChange={(e) =>
                                    setData(
                                        'mother_maiden_name',
                                        e.target.value,
                                    )
                                }
                            />
                            <InputError message={errors.mother_maiden_name} />
                        </Field>

                        <Field>
                            <RequiredLabel htmlFor="guardian_name">
                                Guardian Name
                            </RequiredLabel>
                            <Input
                                id="guardian_name"
                                value={data.guardian_name}
                                onChange={(e) =>
                                    setData('guardian_name', e.target.value)
                                }
                                required
                            />
                            <InputError message={errors.guardian_name} />
                        </Field>

                        <Field>
                            <RequiredLabel htmlFor="primary_contact_number">
                                Primary Contact Number
                            </RequiredLabel>
                            <Input
                                id="primary_contact_number"
                                type="tel"
                                value={data.primary_contact_number}
                                onChange={(e) =>
                                    setData(
                                        'primary_contact_number',
                                        e.target.value,
                                    )
                                }
                                placeholder="e.g. 09171234567"
                                required
                            />
                            <InputError
                                message={errors.primary_contact_number}
                            />
                        </Field>
                    </FormSection>

                    <FormSection
                        title="Social Media"
                        description="Optional social media details."
                    >
                        <Field>
                            <Label htmlFor="facebook_account">
                                Facebook Account Name
                            </Label>
                            <Input
                                id="facebook_account"
                                value={data.facebook_account}
                                onChange={(e) =>
                                    setData(
                                        'facebook_account',
                                        e.target.value,
                                    )
                                }
                            />
                            <InputError message={errors.facebook_account} />
                        </Field>

                        <Field>
                            <Label htmlFor="facebook_profile">
                                Facebook Profile URL or Username
                            </Label>
                            <Input
                                id="facebook_profile"
                                value={data.facebook_profile}
                                onChange={(e) =>
                                    setData('facebook_profile', e.target.value)
                                }
                                placeholder="https://facebook.com/username"
                            />
                            <InputError message={errors.facebook_profile} />
                        </Field>
                    </FormSection>

                    <FormSection
                        title="Previous School History"
                        description="Details about your last school attended."
                    >
                        <Field>
                            <Label htmlFor="last_school_attended">
                                Name of Last School Attended
                            </Label>
                            <Input
                                id="last_school_attended"
                                value={data.last_school_attended}
                                onChange={(e) =>
                                    setData(
                                        'last_school_attended',
                                        e.target.value,
                                    )
                                }
                            />
                            <InputError message={errors.last_school_attended} />
                        </Field>

                        <Field>
                            <Label htmlFor="previous_section">
                                Previous Section
                            </Label>
                            <Input
                                id="previous_section"
                                value={data.previous_section}
                                onChange={(e) =>
                                    setData('previous_section', e.target.value)
                                }
                            />
                            <InputError message={errors.previous_section} />
                        </Field>

                        <Field className="sm:col-span-2">
                            <Label htmlFor="last_school_address">
                                Last School Address
                            </Label>
                            <Textarea
                                id="last_school_address"
                                value={data.last_school_address}
                                onChange={(e) =>
                                    setData(
                                        'last_school_address',
                                        e.target.value,
                                    )
                                }
                                rows={2}
                            />
                            <InputError message={errors.last_school_address} />
                        </Field>

                        <Field>
                            <Label htmlFor="academic_year">
                                Academic Year
                            </Label>
                            <Input
                                id="academic_year"
                                value={data.academic_year}
                                onChange={(e) =>
                                    setData('academic_year', e.target.value)
                                }
                                placeholder="e.g. 2023-2024"
                            />
                            <InputError message={errors.academic_year} />
                        </Field>

                        <Field>
                            <Label htmlFor="completion_date">
                                Completion Date
                            </Label>
                            <Input
                                id="completion_date"
                                type="date"
                                value={data.completion_date}
                                onChange={(e) =>
                                    setData('completion_date', e.target.value)
                                }
                            />
                            {data.completion_date ? (
                                <p className="text-xs text-muted-foreground">
                                    {formatDateDisplay(data.completion_date)}
                                </p>
                            ) : null}
                            <InputError message={errors.completion_date} />
                        </Field>

                        <Field>
                            <Label htmlFor="gwa">
                                General Weighted Average (GWA)
                            </Label>
                            <Input
                                id="gwa"
                                type="number"
                                step="0.01"
                                min={0}
                                max={5}
                                value={data.gwa}
                                onChange={(e) =>
                                    setData('gwa', e.target.value)
                                }
                                placeholder="e.g. 1.75"
                            />
                            <InputError message={errors.gwa} />
                        </Field>
                    </FormSection>

                    <div className="flex justify-end">
                        <Button
                            type="submit"
                            disabled={processing}
                            data-test="information-sheet-submit"
                        >
                            {processing && <Spinner />}
                            {isEditing
                                ? 'Update Information'
                                : 'Save Information'}
                        </Button>
                    </div>
                </form>
            </div>
        </>
    );
}

InformationSheetIndex.layout = (props: { currentTeam?: { slug: string } | null }) => ({
    breadcrumbs: [
        {
            title: 'My Information Sheet',
            href: props.currentTeam
                ? edit(props.currentTeam.slug)
                : '#',
        },
    ],
});
