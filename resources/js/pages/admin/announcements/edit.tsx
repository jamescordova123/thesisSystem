import { Head } from '@inertiajs/react';
import AnnouncementForm from '@/components/announcement-form';
import Heading from '@/components/heading';
import { edit, index, update } from '@/routes/admin/announcements';
import type { AppRoleOption } from '@/types';

type UserOption = {
    id: number;
    name: string;
    email: string;
};

type EditableAnnouncement = {
    id: number;
    title: string;
    body: string;
    image_url: string | null;
    send_to_all: boolean;
    is_published: boolean;
    recipient_ids: number[];
};

type Props = {
    announcement: EditableAnnouncement;
    users: UserOption[];
    roles: AppRoleOption[];
};

export default function AnnouncementEdit({
    announcement,
    users,
    roles,
}: Props) {
    return (
        <>
            <Head title={`Edit ${announcement.title}`} />

            <div className="flex flex-col space-y-6">
                <Heading
                    variant="small"
                    title="Edit announcement"
                    description="Update the announcement content and its audience"
                />

                <AnnouncementForm
                    action={update(announcement.id).url}
                    users={users}
                    roles={roles}
                    initial={announcement}
                    submitLabel="Save changes"
                />
            </div>
        </>
    );
}

AnnouncementEdit.layout = (props: {
    announcement: EditableAnnouncement;
}) => ({
    breadcrumbs: [
        {
            title: 'Announcements',
            href: index(),
        },
        {
            title: props.announcement.title,
            href: edit(props.announcement.id),
        },
    ],
});
