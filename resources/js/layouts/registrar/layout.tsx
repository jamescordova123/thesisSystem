import { Link } from '@inertiajs/react';
import type { PropsWithChildren } from 'react';
import Heading from '@/components/heading';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { useCurrentUrl } from '@/hooks/use-current-url';
import { cn, toUrl } from '@/lib/utils';
import { index as academicHistory } from '@/routes/registrar/academic-history';
import { index as classLists } from '@/routes/registrar/class-lists';
import { dashboard } from '@/routes/registrar';
import { index as enrollments } from '@/routes/registrar/enrollments';
import { index as records } from '@/routes/registrar/records';
import { index as reports } from '@/routes/registrar/reports';
import { search } from '@/routes/registrar';
import { index as sections } from '@/routes/registrar/sections';
import { index as students } from '@/routes/registrar/students';
import type { NavItem } from '@/types';

const sidebarNavItems: NavItem[] = [
    { title: 'Dashboard', href: dashboard(), icon: null },
    { title: 'Students', href: students(), icon: null },
    { title: 'Sections', href: sections(), icon: null },
    { title: 'Manual Enrollment', href: enrollments(), icon: null },
    { title: 'Student Records', href: records(), icon: null },
    { title: 'Class Lists', href: classLists(), icon: null },
    { title: 'Academic History', href: academicHistory(), icon: null },
    { title: 'Reports', href: reports(), icon: null },
    { title: 'Search', href: search(), icon: null },
];

export default function RegistrarLayout({ children }: PropsWithChildren) {
    const { isCurrentOrParentUrl } = useCurrentUrl();

    return (
        <div className="px-4 py-6">
            <Heading
                title="Registrar Dashboard"
                description="Student information and enrollment management"
            />

            <div className="flex flex-col lg:flex-row lg:space-x-8">
                <aside className="w-full shrink-0 lg:w-52">
                    <nav
                        className="flex flex-col space-y-1"
                        aria-label="Registrar"
                    >
                        {sidebarNavItems.map((item, index) => (
                            <Button
                                key={`${toUrl(item.href)}-${index}`}
                                size="sm"
                                variant="ghost"
                                asChild
                                className={cn('w-full justify-start', {
                                    'bg-muted': isCurrentOrParentUrl(item.href),
                                })}
                            >
                                <Link href={item.href}>{item.title}</Link>
                            </Button>
                        ))}
                    </nav>
                </aside>

                <Separator className="my-6 lg:hidden" />

                <div className="min-w-0 flex-1">
                    <section className="max-w-6xl space-y-8">
                        {children}
                    </section>
                </div>
            </div>
        </div>
    );
}
