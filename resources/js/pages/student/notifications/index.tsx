import { Head, router } from '@inertiajs/react';
import { CheckCheck } from 'lucide-react';
import Heading from '@/components/heading';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader } from '@/components/ui/card';
import { read, readAll } from '@/routes/notifications';

type Notification = {
    id: number;
    subject: string;
    message: string;
    type: string;
    sender: string | null;
    sent_at: string | null;
    read_at: string | null;
    status: string;
};

type Props = { notifications: { data: Notification[] } };

export default function StudentNotificationsIndex({ notifications }: Props) {
    const hasUnread = notifications.data.some((n) => n.status === 'sent');

    return (
        <>
            <Head title="Notifications" />
            <div className="mx-auto max-w-3xl space-y-6 p-4">
                <div className="flex items-center justify-between">
                    <Heading variant="small" title="Notifications" description="Financial notices from the cashier office" />
                    {hasUnread && (
                        <Button variant="outline" size="sm" onClick={() => router.post(readAll().url, {}, { preserveScroll: true })}>
                            <CheckCheck className="h-4 w-4" /> Mark all as read
                        </Button>
                    )}
                </div>

                <div className="space-y-4">
                    {notifications.data.map((n) => (
                        <Card key={n.id} className={n.status === 'sent' ? 'border-primary/40' : ''}>
                            <CardHeader>
                                <div className="flex items-start justify-between gap-3">
                                    <div>
                                        <div className="flex items-center gap-2">
                                            <span className="font-semibold">{n.subject}</span>
                                            {n.status === 'sent' && <Badge>New</Badge>}
                                        </div>
                                        <p className="text-xs text-muted-foreground">
                                            {n.sent_at ? new Date(n.sent_at).toLocaleString() : ''}
                                            {n.sender && ` · ${n.sender}`}
                                        </p>
                                    </div>
                                    {n.status === 'sent' && (
                                        <Button variant="ghost" size="sm" onClick={() => router.post(read(n.id).url, {}, { preserveScroll: true })}>
                                            Mark as read
                                        </Button>
                                    )}
                                </div>
                            </CardHeader>
                            <CardContent><p className="whitespace-pre-line text-sm">{n.message}</p></CardContent>
                        </Card>
                    ))}
                    {notifications.data.length === 0 && (
                        <p className="py-16 text-center text-muted-foreground">No notifications yet.</p>
                    )}
                </div>
            </div>
        </>
    );
}

StudentNotificationsIndex.layout = {
    breadcrumbs: [{ title: 'Notifications', href: '/notifications' }],
};
