import { Head, useForm } from '@inertiajs/react';
import { useState } from 'react';
import InputError from '@/components/input-error';
import RequiredLabel from '@/components/required-label';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { Textarea } from '@/components/ui/textarea';
import { index, store } from '@/routes/cashier/notifications';
import type { SelectOption } from '@/types';

type Notification = {
    id: number;
    subject: string;
    message: string;
    type: string;
    student: string;
    student_number: string | null;
    sender: string | null;
    sent_at: string | null;
    status: string;
};

type Props = {
    notifications: { data: Notification[] };
    filters: { search?: string };
    students: { id: number; name: string; student_number: string | null }[];
    notificationTypes: SelectOption[];
};

const selectClass = 'border-input flex h-9 w-full rounded-md border px-3 py-1 text-sm';

export default function NotificationsIndex({ notifications, students, notificationTypes }: Props) {
    const [open, setOpen] = useState(false);
    const { data, setData, post, processing, errors, reset } = useForm({
        user_id: '', type: 'payment_reminder', subject: '', message: '',
    });

    return (
        <>
            <Head title="Notifications" />
            <div className="mb-4 flex items-center justify-between">
                <h2 className="text-lg font-semibold">Send Notifications</h2>
                <Dialog open={open} onOpenChange={setOpen}>
                    <DialogTrigger asChild><Button>Send Notification</Button></DialogTrigger>
                    <DialogContent>
                        <DialogHeader><DialogTitle>Send Notification</DialogTitle></DialogHeader>
                        <form onSubmit={(e) => { e.preventDefault(); post(store().url, { onSuccess: () => { setOpen(false); reset(); } }); }} className="grid gap-3">
                            <div>
                                <RequiredLabel>Student</RequiredLabel>
                                <select className={selectClass} value={data.user_id} onChange={(e) => setData('user_id', e.target.value)} required>
                                    <option value="">Select student</option>
                                    {students.map((s) => <option key={s.id} value={s.id}>{s.student_number} - {s.name}</option>)}
                                </select>
                                <InputError message={errors.user_id} />
                            </div>
                            <div>
                                <RequiredLabel>Type</RequiredLabel>
                                <select className={selectClass} value={data.type} onChange={(e) => setData('type', e.target.value)} required>
                                    {notificationTypes.map((t) => <option key={t.value} value={t.value}>{t.label}</option>)}
                                </select>
                            </div>
                            <div>
                                <RequiredLabel>Subject</RequiredLabel>
                                <Input value={data.subject} onChange={(e) => setData('subject', e.target.value)} required />
                                <InputError message={errors.subject} />
                            </div>
                            <div>
                                <RequiredLabel>Message</RequiredLabel>
                                <Textarea value={data.message} onChange={(e) => setData('message', e.target.value)} required />
                                <InputError message={errors.message} />
                            </div>
                            <Button type="submit" disabled={processing}>{processing && <Spinner />} Send</Button>
                        </form>
                    </DialogContent>
                </Dialog>
            </div>

            <div className="space-y-3">
                {notifications.data.map((n) => (
                    <div key={n.id} className="rounded-lg border p-4">
                        <div className="flex items-start justify-between gap-2">
                            <div>
                                <p className="font-medium">{n.subject}</p>
                                <p className="text-sm text-muted-foreground">{n.student} · {n.type.replace('_', ' ')}</p>
                                <p className="mt-1 text-sm">{n.message}</p>
                            </div>
                            <Badge variant={n.status === 'read' ? 'secondary' : 'default'}>{n.status}</Badge>
                        </div>
                        <p className="mt-2 text-xs text-muted-foreground">
                            {n.sender} · {n.sent_at ? new Date(n.sent_at).toLocaleString() : ''}
                        </p>
                    </div>
                ))}
                {notifications.data.length === 0 && <p className="text-muted-foreground">No notifications sent yet.</p>}
            </div>
        </>
    );
}

NotificationsIndex.layout = { breadcrumbs: [{ title: 'Notifications', href: index() }] };
