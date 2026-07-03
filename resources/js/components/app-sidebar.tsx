import { Link, usePage } from '@inertiajs/react';
import { BookOpen, ClipboardList, FolderGit2, GraduationCap, LayoutGrid, Megaphone, Shield, Wallet } from 'lucide-react';
import AppLogo from '@/components/app-logo';
import { NavFooter } from '@/components/nav-footer';
import { NavMain } from '@/components/nav-main';
import { NavUser } from '@/components/nav-user';
import { TeamSwitcher } from '@/components/team-switcher';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import { index as announcements } from '@/routes/announcements';
import { index as adminAnnouncements } from '@/routes/admin/announcements';
import { edit as informationSheet } from '@/routes/information-sheet';
import { index as adminUsers } from '@/routes/admin/users';
import { dashboard as cashierDashboard } from '@/routes/cashier';
import { dashboard as registrarDashboard } from '@/routes/registrar';
import { index as studentNotifications } from '@/routes/notifications';
import type { NavItem } from '@/types';

export function AppSidebar() {
    const page = usePage();
    const dashboardUrl = page.props.currentTeam
        ? dashboard(page.props.currentTeam.slug)
        : '/';

    const can = page.props.can;
    const isAdmin = can?.canManageUsers ?? false;

    const mainNavItems: NavItem[] = [
        {
            title: 'Dashboard',
            href: dashboardUrl,
            icon: LayoutGrid,
        },
        {
            title: 'Announcements',
            href: can?.canManageAnnouncements
                ? adminAnnouncements()
                : announcements(),
            icon: Megaphone,
        },
        ...(can?.canSubmitThesis && !isAdmin && page.props.currentTeam
            ? [
                  {
                      title: 'My Information Sheet',
                      href: informationSheet(page.props.currentTeam.slug),
                      icon: ClipboardList,
                  },
                  {
                      title: 'Notifications',
                      href: studentNotifications(),
                      icon: Megaphone,
                  },
              ]
            : []),
        ...(can?.canAccessRegistrar && !isAdmin
            ? [
                  {
                      title: 'Registrar',
                      href: registrarDashboard(),
                      icon: GraduationCap,
                  },
              ]
            : []),
        ...(can?.canAccessCashier && !isAdmin
            ? [
                  {
                      title: 'Cashier',
                      href: cashierDashboard(),
                      icon: Wallet,
                  },
              ]
            : []),
        ...(isAdmin
            ? [
                  {
                      title: 'Administration',
                      href: adminUsers(),
                      icon: Shield,
                  },
              ]
            : []),
    ];

    const footerNavItems: NavItem[] = [
        {
            title: 'Repository',
            href: 'https://github.com/laravel/react-starter-kit',
            icon: FolderGit2,
        },
        {
            title: 'Documentation',
            href: 'https://laravel.com/docs/starter-kits#react',
            icon: BookOpen,
        },
    ];

    return (
        <Sidebar collapsible="icon" variant="inset">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link href={dashboardUrl} prefetch>
                                <AppLogo />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <TeamSwitcher />
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent>
                <NavMain items={mainNavItems} />
            </SidebarContent>

            <SidebarFooter>
                <NavFooter items={footerNavItems} className="mt-auto" />
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
