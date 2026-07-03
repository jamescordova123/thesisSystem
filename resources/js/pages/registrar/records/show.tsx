import { Head, useForm } from '@inertiajs/react';
import InputError from '@/components/input-error';
import RequiredLabel from '@/components/required-label';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { Textarea } from '@/components/ui/textarea';
import { download, index, store } from '@/routes/registrar/records';

type Props = {
    student: { id: number; student_number: string | null; name: string };
    records: {
        id: number;
        title: string;
        record_type: string;
        notes: string | null;
        has_file: boolean;
        uploaded_by: string | null;
        created_at: string | null;
    }[];
    recordTypes: string[];
};

const selectClass = 'border-input flex h-9 w-full rounded-md border px-3 py-1 text-sm';

export default function RecordsShow({ student, records, recordTypes }: Props) {
    const { data, setData, post, processing, errors, reset } = useForm({
        title: '',
        record_type: 'enrollment_requirement',
        notes: '',
        file: null as File | null,
    });

    return (
        <>
            <Head title={`Records - ${student.name}`} />
            <h2 className="text-lg font-semibold">{student.name}</h2>
            <p className="mb-6 text-sm text-muted-foreground">{student.student_number}</p>

            <form
                onSubmit={(e) => {
                    e.preventDefault();
                    post(store(student.id).url, {
                        forceFormData: true,
                        onSuccess: () => reset(),
                    });
                }}
                className="mb-8 grid max-w-2xl gap-3 rounded-lg border p-4 sm:grid-cols-2"
            >
                <h3 className="text-sm font-medium sm:col-span-2">Upload Record</h3>
                <div className="sm:col-span-2">
                    <RequiredLabel htmlFor="title">Title</RequiredLabel>
                    <Input
                        id="title"
                        value={data.title}
                        onChange={(e) => setData('title', e.target.value)}
                        required
                    />
                    <InputError message={errors.title} />
                </div>
                <div>
                    <RequiredLabel htmlFor="record_type">Record Type</RequiredLabel>
                    <select
                        id="record_type"
                        className={selectClass}
                        value={data.record_type}
                        onChange={(e) => setData('record_type', e.target.value)}
                        required
                    >
                        {recordTypes.map((type) => (
                            <option key={type} value={type}>
                                {type.replace('_', ' ')}
                            </option>
                        ))}
                    </select>
                </div>
                <div>
                    <Label htmlFor="file">File (PDF, DOC, JPG, PNG)</Label>
                    <Input
                        id="file"
                        type="file"
                        onChange={(e) => setData('file', e.target.files?.[0] ?? null)}
                    />
                    <InputError message={errors.file} />
                </div>
                <div className="sm:col-span-2">
                    <Label htmlFor="notes">Notes</Label>
                    <Textarea
                        id="notes"
                        value={data.notes}
                        onChange={(e) => setData('notes', e.target.value)}
                    />
                </div>
                <div className="sm:col-span-2">
                    <Button type="submit" disabled={processing}>
                        {processing && <Spinner />} Upload Record
                    </Button>
                </div>
            </form>

            <div className="space-y-3">
                {records.map((record) => (
                    <div key={record.id} className="rounded-lg border p-4">
                        <div className="flex items-start justify-between gap-4">
                            <div>
                                <p className="font-medium">{record.title}</p>
                                <p className="text-sm text-muted-foreground">
                                    {record.record_type.replace('_', ' ')} · {record.uploaded_by}
                                </p>
                                {record.notes && <p className="mt-1 text-sm">{record.notes}</p>}
                            </div>
                            {record.has_file && (
                                <Button variant="outline" size="sm" asChild>
                                    <a href={download({ user: student.id, record: record.id }).url}>
                                        Download
                                    </a>
                                </Button>
                            )}
                        </div>
                    </div>
                ))}
                {records.length === 0 && (
                    <p className="text-muted-foreground">No records uploaded yet.</p>
                )}
            </div>
        </>
    );
}

RecordsShow.layout = (props: Props) => ({
    breadcrumbs: [
        { title: 'Records', href: index() },
        { title: props.student.name, href: '#' },
    ],
});
