import { Head } from '@inertiajs/react';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { index as classLists } from '@/routes/registrar/class-lists';
import {
    enrolledVsNot,
    enrollmentSummary,
    index,
    sectionCapacity,
    studentMaster,
    studentsPerSection,
} from '@/routes/registrar/exports';

type Report = {
    key: string;
    label: string;
    route?: string;
    count?: number;
    enrolled?: number;
    not_enrolled?: number;
};

type Props = { reportTypes: Report[] };

const exportRoutes: Record<string, { print?: string; csv: string }> = {
    'class-list': { print: classLists().url },
    'enrollment-summary': { csv: enrollmentSummary().url },
    'student-master': {
        print: studentMaster().url,
        csv: `${studentMaster().url}?format=csv`,
    },
    'students-per-section': { csv: studentsPerSection().url },
    'enrolled-vs-not': { csv: enrolledVsNot().url },
    'section-capacity': { csv: sectionCapacity().url },
};

export default function ReportsIndex({ reportTypes }: Props) {
    return (
        <>
            <Head title="Reports" />
            <h2 className="mb-4 text-lg font-semibold">Reports</h2>
            <div className="grid gap-4 sm:grid-cols-2">
                {reportTypes.map((report) => {
                    const exports = exportRoutes[report.key];

                    return (
                        <Card key={report.key}>
                            <CardHeader>
                                <CardTitle className="text-base">{report.label}</CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-3">
                                {report.count !== undefined && (
                                    <p className="text-2xl font-bold">{report.count}</p>
                                )}
                                {report.enrolled !== undefined && (
                                    <p className="text-sm text-muted-foreground">
                                        Enrolled: {report.enrolled} · Not enrolled:{' '}
                                        {report.not_enrolled}
                                    </p>
                                )}
                                {exports && (
                                    <div className="flex flex-wrap gap-2">
                                        {exports.print && (
                                            <Button variant="outline" size="sm" asChild>
                                                <a
                                                    href={exports.print}
                                                    target="_blank"
                                                    rel="noreferrer"
                                                >
                                                    Print / PDF
                                                </a>
                                            </Button>
                                        )}
                                        <Button variant="secondary" size="sm" asChild>
                                            <a href={exports.csv}>Export Excel</a>
                                        </Button>
                                    </div>
                                )}
                            </CardContent>
                        </Card>
                    );
                })}
            </div>
        </>
    );
}

ReportsIndex.layout = { breadcrumbs: [{ title: 'Reports', href: index() }] };
