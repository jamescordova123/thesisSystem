import { Head, Link, router } from '@inertiajs/react';
import { Megaphone, Pencil, Plus, Trash2, Users } from 'lucide-react';
import Heading from '@/components/heading';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    create,
    destroy,
    edit,
    index,
} from '@/routes/admin/announcements';
import type { AdminAnnouncement } from '@/types';

type Props = {
    announcements: AdminAnnouncement[];
};

export default function AnnouncementsIndex({ announcements }: Props) {
    const handleDelete = (announcement: AdminAnnouncement) => {
        if (
            !window.confirm(
                `Delete the announcement "${announcement.title}"? This cannot be undone.`,
            )
        ) {
            return;
        }

        router.delete(destroy(announcement.id).url, { preserveScroll: true });
    };

    return (
        <>
            <Head title="Announcements" />

            <h1 className="sr-only">Announcements</h1>

            <div className="flex flex-col space-y-6">
                <div className="flex items-center justify-between">
                    <Heading
                        variant="small"
                        title="Announcements"
                        description="Broadcast news and notifications to your users"
                    />

                    <Button asChild data-test="create-announcement-button">
                        <Link href={create()}>
                            <Plus /> New announcement
                        </Link>
                    </Button>
                </div>

                <div className="space-y-3">
                    {announcements.map((announcement) => (
                        <div
                            key={announcement.id}
                            data-test="announcement-row"
                            className="flex gap-4 rounded-lg border p-4"
                        >
                            <div className="hidden h-16 w-24 shrink-0 items-center justify-center overflow-hidden rounded-md bg-muted sm:flex">
                                {announcement.image_url ? (
                                    <img
                                        src={announcement.image_url}
                                        alt=""
                                        className="h-full w-full object-cover"
                                    />
                                ) : (
                                    <Megaphone className="h-6 w-6 text-muted-foreground" />
                                )}
                            </div>

                            <div className="min-w-0 flex-1">
                                <div className="flex flex-wrap items-center gap-2">
                                    <span className="font-medium">
                                        {announcement.title}
                                    </span>
                                    {announcement.is_published ? (
                                        <Badge variant="default">
                                            Published
                                        </Badge>
                                    ) : (
                                        <Badge variant="secondary">Draft</Badge>
                                    )}
                                </div>
                                <p className="mt-1 line-clamp-2 text-sm text-muted-foreground">
                                    {announcement.body}
                                </p>
                                <div className="mt-2 flex flex-wrap items-center gap-3 text-xs text-muted-foreground">
                                    <span className="inline-flex items-center gap-1">
                                        <Users className="h-3.5 w-3.5" />
                                        {announcement.send_to_all
                                            ? 'All users'
                                            : `${announcement.recipients_count} recipients`}
                                    </span>
                                    <span>
                                        {announcement.read_count} /{' '}
                                        {announcement.recipients_count} read
                                    </span>
                                    {announcement.author && (
                                        <span>by {announcement.author}</span>
                                    )}
                                </div>
                            </div>

                            <div className="flex shrink-0 items-start gap-1">
                                <Button variant="ghost" size="sm" asChild>
                                    <Link href={edit(announcement.id)}>
                                        <Pencil className="h-4 w-4" />
                                    </Link>
                                </Button>
                                <Button
                                    variant="ghost"
                                    size="sm"
                                    onClick={() => handleDelete(announcement)}
                                    data-test="announcement-delete-button"
                                >
                                    <Trash2 className="h-4 w-4 text-destructive" />
                                </Button>
                            </div>
                        </div>
                    ))}

                    {announcements.length === 0 && (
                        <p className="py-8 text-center text-muted-foreground">
                            No announcements yet. Create your first one.
                        </p>
                    )}
                </div>
            </div>
        </>
    );
}

AnnouncementsIndex.layout = {
    breadcrumbs: [
        {
            title: 'Announcements',
            href: index(),
        },
    ],
};
