import { Link, usePage } from '@inertiajs/react';
import type { PropsWithChildren } from 'react';
import Heading from '@/components/heading';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { useCurrentUrl } from '@/hooks/use-current-url';
import { cn, toUrl } from '@/lib/utils';
import { index as announcements } from '@/routes/admin/announcements';
import { index as roles } from '@/routes/admin/roles';
import { index as users } from '@/routes/admin/users';
import type { NavItem } from '@/types';

export default function AdminLayout({ children }: PropsWithChildren) {
    const { isCurrentOrParentUrl } = useCurrentUrl();
    const page = usePage();

    const sidebarNavItems: NavItem[] = [
        {
            title: 'Users',
            href: users(),
            icon: null,
        },
        {
            title: 'Roles',
            href: roles(),
            icon: null,
        },
        ...(page.props.can?.canManageAnnouncements
            ? [
                  {
                      title: 'Announcements',
                      href: announcements(),
                      icon: null,
                  },
              ]
            : []),
    ];

    return (
        <div className="px-4 py-6">
            <Heading
                title="Administration"
                description="Manage application users, roles, and permissions"
            />

            <div className="flex flex-col lg:flex-row lg:space-x-12">
                <aside className="w-full max-w-xl lg:w-48">
                    <nav
                        className="flex flex-col space-y-1 space-x-0"
                        aria-label="Administration"
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
                                <Link href={item.href}>
                                    {item.icon && (
                                        <item.icon className="h-4 w-4" />
                                    )}
                                    {item.title}
                                </Link>
                            </Button>
                        ))}
                    </nav>
                </aside>

                <Separator className="my-6 lg:hidden" />

                <div className="flex-1 md:max-w-2xl">
                    <section className="max-w-xl space-y-12">
                        {children}
                    </section>
                </div>
            </div>
        </div>
    );
}
