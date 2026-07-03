import { Head } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { classList as exportClassList } from '@/routes/registrar/exports';
import { index } from '@/routes/registrar/class-lists';

type Props = {
    section: {
        id: number;
        name: string;
        program: string;
        year_level: number;
        semester: string;
        academic_year: string;
    };
    students: {
        student_number: string | null;
        name: string;
        enrollment_status: string | null;
    }[];
};

export default function ClassListShow({ section, students }: Props) {
    const printUrl = exportClassList(section.id).url;
    const csvUrl = `${printUrl}?format=csv`;

    return (
        <>
            <Head title={`Class List - ${section.name}`} />
            <div className="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between print:hidden">
                <div>
                    <h2 className="text-lg font-semibold">{section.name}</h2>
                    <p className="text-sm text-muted-foreground">
                        {section.program} · Year {section.year_level} · {section.semester} ·{' '}
                        {section.academic_year}
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
                    <Button variant="ghost" size="sm" onClick={() => window.print()}>
                        Quick Print
                    </Button>
                </div>
            </div>

            <table className="w-full overflow-hidden rounded-lg border text-sm">
                <thead className="bg-muted/50">
                    <tr>
                        <th className="px-4 py-2 text-left">#</th>
                        <th className="px-4 py-2 text-left">Student ID</th>
                        <th className="px-4 py-2 text-left">Name</th>
                        <th className="px-4 py-2 text-left">Status</th>
                    </tr>
                </thead>
                <tbody>
                    {students.map((student, index) => (
                        <tr key={index} className="border-t">
                            <td className="px-4 py-2">{index + 1}</td>
                            <td className="px-4 py-2 font-mono text-xs">
                                {student.student_number}
                            </td>
                            <td className="px-4 py-2">{student.name}</td>
                            <td className="px-4 py-2 capitalize">
                                {student.enrollment_status?.replace('_', ' ')}
                            </td>
                        </tr>
                    ))}
                </tbody>
            </table>
        </>
    );
}

ClassListShow.layout = (props: Props) => ({
    breadcrumbs: [
        { title: 'Class Lists', href: index() },
        { title: props.section.name, href: '#' },
    ],
});
