import { Head, Link } from '@inertiajs/react';
import { GraduationCap, Layers, Users, UserX } from 'lucide-react';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { dashboard } from '@/routes/registrar';

type Stats = {
    totalStudents: number;
    enrolledStudents: number;
    notEnrolledStudents: number;
    totalSections: number;
};

type Enrollment = {
    id: number;
    student: string;
    student_number: string | null;
    program: string;
    section: string | null;
    status: string;
    enrolled_at: string | null;
};

type Activity = {
    action: string;
    user: string | null;
    created_at: string | null;
};

type Props = {
    stats: Stats;
    recentEnrollments: Enrollment[];
    recentActivity: Activity[];
};

export default function RegistrarDashboard({
    stats,
    recentEnrollments,
    recentActivity,
}: Props) {
    const cards = [
        {
            title: 'Total Students',
            value: stats.totalStudents,
            icon: Users,
        },
        {
            title: 'Enrolled Students',
            value: stats.enrolledStudents,
            icon: GraduationCap,
        },
        {
            title: 'Not Enrolled',
            value: stats.notEnrolledStudents,
            icon: UserX,
        },
        {
            title: 'Total Sections',
            value: stats.totalSections,
            icon: Layers,
        },
    ];

    return (
        <>
            <Head title="Registrar Dashboard" />

            <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                {cards.map((card) => (
                    <Card key={card.title}>
                        <CardHeader className="flex flex-row items-center justify-between pb-2">
                            <CardTitle className="text-sm font-medium text-muted-foreground">
                                {card.title}
                            </CardTitle>
                            <card.icon className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">
                                {card.value}
                            </div>
                        </CardContent>
                    </Card>
                ))}
            </div>

            <div className="grid gap-6 lg:grid-cols-2">
                <Card>
                    <CardHeader>
                        <CardTitle>Recent Enrollments</CardTitle>
                    </CardHeader>
                    <CardContent className="space-y-3">
                        {recentEnrollments.length === 0 ? (
                            <p className="text-sm text-muted-foreground">
                                No recent enrollment activity.
                            </p>
                        ) : (
                            recentEnrollments.map((item) => (
                                <div
                                    key={item.id}
                                    className="flex items-center justify-between border-b pb-2 last:border-0"
                                >
                                    <div>
                                        <p className="text-sm font-medium">
                                            {item.student}
                                        </p>
                                        <p className="text-xs text-muted-foreground">
                                            {item.student_number} ·{' '}
                                            {item.program}
                                            {item.section
                                                ? ` · ${item.section}`
                                                : ''}
                                        </p>
                                    </div>
                                    <span className="rounded-full bg-muted px-2 py-0.5 text-xs capitalize">
                                        {item.status.replace('_', ' ')}
                                    </span>
                                </div>
                            ))
                        )}
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Recent Activity</CardTitle>
                    </CardHeader>
                    <CardContent className="space-y-3">
                        {recentActivity.length === 0 ? (
                            <p className="text-sm text-muted-foreground">
                                No recent registrar activity.
                            </p>
                        ) : (
                            recentActivity.map((item, index) => (
                                <div
                                    key={index}
                                    className="border-b pb-2 text-sm last:border-0"
                                >
                                    <p className="font-medium">
                                        {item.action}
                                    </p>
                                    <p className="text-xs text-muted-foreground">
                                        {item.user ?? 'System'} ·{' '}
                                        {item.created_at
                                            ? new Date(
                                                  item.created_at,
                                              ).toLocaleString()
                                            : ''}
                                    </p>
                                </div>
                            ))
                        )}
                    </CardContent>
                </Card>
            </div>
        </>
    );
}

RegistrarDashboard.layout = {
    breadcrumbs: [{ title: 'Registrar', href: dashboard() }],
};
