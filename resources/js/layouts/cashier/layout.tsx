import { Link } from '@inertiajs/react';
import type { PropsWithChildren } from 'react';
import Heading from '@/components/heading';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { useCurrentUrl } from '@/hooks/use-current-url';
import { cn, toUrl } from '@/lib/utils';
import { dashboard } from '@/routes/cashier';
import { index as notifications } from '@/routes/cashier/notifications';
import { index as payments } from '@/routes/cashier/payments';
import { index as reports } from '@/routes/cashier/reports';
import { search } from '@/routes/cashier';
import type { NavItem } from '@/types';

const sidebarNavItems: NavItem[] = [
    { title: 'Dashboard', href: dashboard(), icon: null },
    { title: 'Student Payments', href: payments(), icon: null },
    { title: 'Search', href: search(), icon: null },
    { title: 'Notifications', href: notifications(), icon: null },
    { title: 'Reports', href: reports(), icon: null },
];

export default function CashierLayout({ children }: PropsWithChildren) {
    const { isCurrentOrParentUrl } = useCurrentUrl();

    return (
        <div className="px-4 py-6">
            <Heading
                title="Cashier Dashboard"
                description="Student payments and financial management"
            />

            <div className="flex flex-col lg:flex-row lg:space-x-8">
                <aside className="w-full shrink-0 lg:w-52">
                    <nav className="flex flex-col space-y-1" aria-label="Cashier">
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
                    <section className="max-w-6xl space-y-8">{children}</section>
                </div>
            </div>
        </div>
    );
}
