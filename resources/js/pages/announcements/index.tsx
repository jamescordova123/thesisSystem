import { Head, router } from '@inertiajs/react';
import { CheckCheck, Megaphone } from 'lucide-react';
import Heading from '@/components/heading';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader } from '@/components/ui/card';
import { read, readAll } from '@/routes/announcements';
import type { UserAnnouncement } from '@/types';

type Props = {
    announcements: UserAnnouncement[];
};

function formatDate(value: string | null): string {
    if (!value) {
        return '';
    }

    return new Date(value).toLocaleDateString(undefined, {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
}

export default function AnnouncementsPage({ announcements }: Props) {
    const hasUnread = announcements.some(
        (announcement) => announcement.read_at === null,
    );

    const markRead = (id: number) => {
        router.post(read(id).url, {}, { preserveScroll: true });
    };

    const markAllRead = () => {
        router.post(readAll().url, {}, { preserveScroll: true });
    };

    return (
        <>
            <Head title="Announcements" />

            <div className="mx-auto flex w-full max-w-3xl flex-col space-y-6 p-4">
                <div className="flex items-center justify-between">
                    <Heading
                        variant="small"
                        title="Announcements"
                        description="News and notifications from your administrators"
                    />

                    {hasUnread && (
                        <Button
                            variant="outline"
                            size="sm"
                            onClick={markAllRead}
                            data-test="mark-all-read"
                        >
                            <CheckCheck className="h-4 w-4" /> Mark all as read
                        </Button>
                    )}
                </div>

                <div className="space-y-4">
                    {announcements.map((announcement) => {
                        const unread = announcement.read_at === null;

                        return (
                            <Card
                                key={announcement.id}
                                data-test="user-announcement"
                                className={
                                    unread ? 'border-primary/40 gap-4' : 'gap-4'
                                }
                            >
                                {announcement.image_url && (
                                    <img
                                        src={announcement.image_url}
                                        alt=""
                                        className="max-h-64 w-full rounded-t-xl object-cover"
                                    />
                                )}
                                <CardHeader>
                                    <div className="flex items-start justify-between gap-3">
                                        <div className="space-y-1">
                                            <div className="flex items-center gap-2">
                                                <span className="text-lg font-semibold">
                                                    {announcement.title}
                                                </span>
                                                {unread && (
                                                    <Badge variant="default">
                                                        New
                                                    </Badge>
                                                )}
                                            </div>
                                            <p className="text-xs text-muted-foreground">
                                                {formatDate(
                                                    announcement.published_at,
                                                )}
                                                {announcement.author &&
                                                    ` · ${announcement.author}`}
                                            </p>
                                        </div>
                                        {unread && (
                                            <Button
                                                variant="ghost"
                                                size="sm"
                                                onClick={() =>
                                                    markRead(announcement.id)
                                                }
                                            >
                                                Mark as read
                                            </Button>
                                        )}
                                    </div>
                                </CardHeader>
                                <CardContent>
                                    <p className="whitespace-pre-line text-sm text-foreground/90">
                                        {announcement.body}
                                    </p>
                                </CardContent>
                            </Card>
                        );
                    })}

                    {announcements.length === 0 && (
                        <div className="flex flex-col items-center gap-2 py-16 text-center text-muted-foreground">
                            <Megaphone className="h-8 w-8" />
                            <p>You have no announcements yet.</p>
                        </div>
                    )}
                </div>
            </div>
        </>
    );
}

AnnouncementsPage.layout = {
    breadcrumbs: [
        {
            title: 'Announcements',
            href: '/announcements',
        },
    ],
};
