import { Head } from '@inertiajs/react';
import AnnouncementForm from '@/components/announcement-form';
import Heading from '@/components/heading';
import { index, store } from '@/routes/admin/announcements';
import type { AppRoleOption } from '@/types';

type UserOption = {
    id: number;
    name: string;
    email: string;
};

type Props = {
    users: UserOption[];
    roles: AppRoleOption[];
};

export default function AnnouncementCreate({ users, roles }: Props) {
    return (
        <>
            <Head title="New announcement" />

            <div className="flex flex-col space-y-6">
                <Heading
                    variant="small"
                    title="New announcement"
                    description="Compose an announcement and choose who should see it"
                />

                <AnnouncementForm
                    action={store().url}
                    users={users}
                    roles={roles}
                    submitLabel="Create announcement"
                />
            </div>
        </>
    );
}

AnnouncementCreate.layout = {
    breadcrumbs: [
        {
            title: 'Announcements',
            href: index(),
        },
        {
            title: 'New',
            href: '#',
        },
    ],
};
