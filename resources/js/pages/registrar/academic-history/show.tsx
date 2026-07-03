import { Head } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { academicHistory as exportAcademicHistory } from '@/routes/registrar/exports';
import { index } from '@/routes/registrar/academic-history';

type Props = {
    student: { id: number; student_number: string | null; name: string; program: string | null };
    histories: {
        subject_code: string;
        subject_name: string;
        grade: string | null;
        gpa: string | null;
        gwa: string | null;
        academic_standing: string | null;
        academic_year: string | null;
        semester: string | null;
    }[];
};

export default function AcademicHistoryShow({ student, histories }: Props) {
    const printUrl = exportAcademicHistory(student.id).url;
    const csvUrl = `${printUrl}?format=csv`;

    return (
        <>
            <Head title={`History - ${student.name}`} />
            <div className="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between print:hidden">
                <div>
                    <h2 className="text-lg font-semibold">{student.name}</h2>
                    <p className="text-sm text-muted-foreground">
                        {student.student_number} · {student.program}
                    </p>
                </div>
                <div className="flex gap-2">
                    <Button variant="outline" size="sm" asChild>
                        <a href={printUrl} target="_blank" rel="noreferrer">
                            Print / PDF
                        </a>
                    </Button>
                    <Button variant="secondary" size="sm" asChild>
                        <a href={csvUrl}>Export Excel</a>
                    </Button>
                </div>
            </div>

            <table className="w-full rounded-lg border text-sm">
                <thead className="bg-muted/50">
                    <tr>
                        <th className="px-3 py-2 text-left">Subject</th>
                        <th className="px-3 py-2 text-left">Grade</th>
                        <th className="px-3 py-2 text-left">Term</th>
                        <th className="px-3 py-2 text-left">Standing</th>
                    </tr>
                </thead>
                <tbody>
                    {histories.map((history, index) => (
                        <tr key={index} className="border-t">
                            <td className="px-3 py-2">
                                {history.subject_code} - {history.subject_name}
                            </td>
                            <td className="px-3 py-2">{history.grade ?? '—'}</td>
                            <td className="px-3 py-2">
                                {history.academic_year} {history.semester}
                            </td>
                            <td className="px-3 py-2">{history.academic_standing ?? '—'}</td>
                        </tr>
                    ))}
                    {histories.length === 0 && (
                        <tr>
                            <td colSpan={4} className="px-3 py-6 text-center text-muted-foreground">
                                No academic history records.
                            </td>
                        </tr>
                    )}
                </tbody>
            </table>
        </>
    );
}

AcademicHistoryShow.layout = (props: Props) => ({
    breadcrumbs: [
        { title: 'Academic History', href: index() },
        { title: props.student.name, href: '#' },
    ],
});
