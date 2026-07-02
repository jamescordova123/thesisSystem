import { Head, Link } from '@inertiajs/react';
import { Pencil, Plus } from 'lucide-react';
import CreateUserModal from '@/components/create-user-modal';
import Heading from '@/components/heading';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger,
} from '@/components/ui/tooltip';
import { edit, index } from '@/routes/admin/users';
import type { AdminUser, AppRoleOption } from '@/types';

type Props = {
    users: AdminUser[];
    availableRoles: AppRoleOption[];
};

export default function UsersIndex({ users, availableRoles }: Props) {
    return (
        <>
            <Head title="Users" />

            <h1 className="sr-only">Users</h1>

            <div className="flex flex-col space-y-6">
                <div className="flex items-center justify-between">
                    <Heading
                        variant="small"
                        title="Users"
                        description="Manage user roles across the application"
                    />

                    <CreateUserModal availableRoles={availableRoles}>
                        <Button data-test="create-user-button">
                            <Plus /> New user
                        </Button>
                    </CreateUserModal>
                </div>

                <div className="space-y-3">
                    {users.map((user) => (
                        <div
                            key={user.id}
                            data-test="user-row"
                            className="flex items-center justify-between gap-4 rounded-lg border p-4"
                        >
                            <div>
                                <div className="font-medium">{user.name}</div>
                                <div className="text-sm text-muted-foreground">
                                    {user.email}
                                </div>
                                <div className="mt-2 flex flex-wrap gap-2">
                                    {user.role ? (
                                        <Badge
                                            key={user.role.value}
                                            variant="secondary"
                                        >
                                            {user.role.label}
                                        </Badge>
                                    ) : null}
                                </div>
                            </div>

                            <TooltipProvider>
                                <Tooltip>
                                    <TooltipTrigger asChild>
                                        <Button
                                            variant="ghost"
                                            size="sm"
                                            data-test="user-edit-button"
                                            asChild
                                        >
                                            <Link href={edit(user.id)}>
                                                <Pencil className="h-4 w-4" />
                                            </Link>
                                        </Button>
                                    </TooltipTrigger>
                                    <TooltipContent>
                                        <p>Edit roles</p>
                                    </TooltipContent>
                                </Tooltip>
                            </TooltipProvider>
                        </div>
                    ))}

                    {users.length === 0 ? (
                        <p className="py-8 text-center text-muted-foreground">
                            No users found.
                        </p>
                    ) : null}
                </div>
            </div>
        </>
    );
}

UsersIndex.layout = {
    breadcrumbs: [
        {
            title: 'Users',
            href: index(),
        },
    ],
};
