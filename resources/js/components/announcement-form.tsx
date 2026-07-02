import { useForm } from '@inertiajs/react';
import { ImageUp, Search, X } from 'lucide-react';
import { useMemo, useState } from 'react';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { Textarea } from '@/components/ui/textarea';
import type { AppRoleOption } from '@/types';

type UserOption = {
    id: number;
    name: string;
    email: string;
};

type AnnouncementInitial = {
    title: string;
    body: string;
    image_url: string | null;
    send_to_all: boolean;
    is_published: boolean;
    recipient_ids: number[];
};

type Props = {
    action: string;
    users: UserOption[];
    roles: AppRoleOption[];
    initial?: AnnouncementInitial;
    submitLabel: string;
};

export default function AnnouncementForm({
    action,
    users,
    roles,
    initial,
    submitLabel,
}: Props) {
    const { data, setData, post, processing, errors } = useForm({
        title: initial?.title ?? '',
        body: initial?.body ?? '',
        image: null as File | null,
        remove_image: false,
        send_to_all: initial?.send_to_all ?? false,
        publish: initial ? initial.is_published : true,
        user_ids: initial?.recipient_ids ?? ([] as number[]),
        role_ids: [] as string[],
    });

    const [search, setSearch] = useState('');
    const [preview, setPreview] = useState<string | null>(
        initial?.image_url ?? null,
    );

    const filteredUsers = useMemo(() => {
        const term = search.trim().toLowerCase();

        if (!term) {
            return users;
        }

        return users.filter(
            (user) =>
                user.name.toLowerCase().includes(term) ||
                user.email.toLowerCase().includes(term),
        );
    }, [search, users]);

    const toggleUser = (id: number, checked: boolean) => {
        setData(
            'user_ids',
            checked
                ? [...data.user_ids, id]
                : data.user_ids.filter((value) => value !== id),
        );
    };

    const toggleRole = (value: string, checked: boolean) => {
        setData(
            'role_ids',
            checked
                ? [...data.role_ids, value]
                : data.role_ids.filter((role) => role !== value),
        );
    };

    const handleImage = (event: React.ChangeEvent<HTMLInputElement>) => {
        const file = event.target.files?.[0] ?? null;
        setData('image', file);

        if (file) {
            setData('remove_image', false);
            setPreview(URL.createObjectURL(file));
        }
    };

    const clearImage = () => {
        setData('image', null);
        setData('remove_image', true);
        setPreview(null);
    };

    const submit = (event: React.FormEvent) => {
        event.preventDefault();
        post(action, { forceFormData: true, preserveScroll: true });
    };

    return (
        <form onSubmit={submit} className="space-y-8">
            <div className="grid gap-2">
                <Label htmlFor="title">Title</Label>
                <Input
                    id="title"
                    value={data.title}
                    onChange={(event) => setData('title', event.target.value)}
                    placeholder="Enrollment schedule for the new semester"
                    data-test="announcement-title"
                    required
                />
                <InputError message={errors.title} />
            </div>

            <div className="grid gap-2">
                <Label htmlFor="body">Message</Label>
                <Textarea
                    id="body"
                    value={data.body}
                    onChange={(event) => setData('body', event.target.value)}
                    placeholder="Write the announcement details here..."
                    rows={6}
                    data-test="announcement-body"
                    required
                />
                <InputError message={errors.body} />
            </div>

            <div className="grid gap-2">
                <Label>Banner image</Label>
                <div className="space-y-3">
                    {preview ? (
                        <div className="relative overflow-hidden rounded-lg border">
                            <img
                                src={preview}
                                alt="Banner preview"
                                className="max-h-56 w-full object-cover"
                            />
                            <Button
                                type="button"
                                variant="secondary"
                                size="sm"
                                className="absolute top-2 right-2"
                                onClick={clearImage}
                            >
                                <X className="h-4 w-4" /> Remove
                            </Button>
                        </div>
                    ) : (
                        <label className="flex cursor-pointer flex-col items-center justify-center gap-2 rounded-lg border border-dashed p-8 text-center text-sm text-muted-foreground transition hover:bg-muted/50">
                            <ImageUp className="h-6 w-6" />
                            <span>
                                Click to upload a banner (JPG, PNG, or WEBP up
                                to 4MB)
                            </span>
                            <input
                                type="file"
                                accept="image/jpeg,image/png,image/webp"
                                className="hidden"
                                onChange={handleImage}
                                data-test="announcement-image"
                            />
                        </label>
                    )}
                    <InputError message={errors.image} />
                </div>
            </div>

            <div className="space-y-4 rounded-lg border p-4">
                <div className="flex items-start gap-3">
                    <Checkbox
                        id="send_to_all"
                        checked={data.send_to_all}
                        onCheckedChange={(checked) =>
                            setData('send_to_all', checked === true)
                        }
                        data-test="announcement-send-to-all"
                    />
                    <div>
                        <Label htmlFor="send_to_all">Send to all users</Label>
                        <p className="text-sm text-muted-foreground">
                            Everyone with an account will receive this
                            announcement.
                        </p>
                    </div>
                </div>

                {!data.send_to_all && (
                    <div className="space-y-5 border-t pt-4">
                        <div className="space-y-2">
                            <Label>Target by role</Label>
                            <div className="flex flex-wrap gap-3">
                                {roles.map((role) => (
                                    <label
                                        key={role.value}
                                        className="flex items-center gap-2 rounded-md border px-3 py-1.5 text-sm"
                                    >
                                        <Checkbox
                                            checked={data.role_ids.includes(
                                                role.value,
                                            )}
                                            onCheckedChange={(checked) =>
                                                toggleRole(
                                                    role.value,
                                                    checked === true,
                                                )
                                            }
                                            data-test={`announcement-role-${role.value}`}
                                        />
                                        {role.label}
                                    </label>
                                ))}
                            </div>
                        </div>

                        <div className="space-y-2">
                            <div className="flex items-center justify-between">
                                <Label>Target specific users</Label>
                                <span className="text-sm text-muted-foreground">
                                    {data.user_ids.length} selected
                                </span>
                            </div>
                            <div className="relative">
                                <Search className="absolute top-2.5 left-2 h-4 w-4 text-muted-foreground" />
                                <Input
                                    value={search}
                                    onChange={(event) =>
                                        setSearch(event.target.value)
                                    }
                                    placeholder="Search users by name or email"
                                    className="pl-8"
                                />
                            </div>
                            <div className="max-h-64 space-y-1 overflow-y-auto rounded-md border p-2">
                                {filteredUsers.map((user) => (
                                    <label
                                        key={user.id}
                                        className="flex cursor-pointer items-center gap-3 rounded-md p-2 hover:bg-muted/50"
                                    >
                                        <Checkbox
                                            checked={data.user_ids.includes(
                                                user.id,
                                            )}
                                            onCheckedChange={(checked) =>
                                                toggleUser(
                                                    user.id,
                                                    checked === true,
                                                )
                                            }
                                            data-test={`announcement-user-${user.id}`}
                                        />
                                        <span className="flex flex-col">
                                            <span className="text-sm font-medium">
                                                {user.name}
                                            </span>
                                            <span className="text-xs text-muted-foreground">
                                                {user.email}
                                            </span>
                                        </span>
                                    </label>
                                ))}
                                {filteredUsers.length === 0 && (
                                    <p className="py-4 text-center text-sm text-muted-foreground">
                                        No users match your search.
                                    </p>
                                )}
                            </div>
                        </div>

                        <InputError message={errors.user_ids} />
                    </div>
                )}
            </div>

            <div className="flex items-start gap-3">
                <Checkbox
                    id="publish"
                    checked={data.publish}
                    onCheckedChange={(checked) =>
                        setData('publish', checked === true)
                    }
                    data-test="announcement-publish"
                />
                <div>
                    <Label htmlFor="publish">Publish immediately</Label>
                    <p className="text-sm text-muted-foreground">
                        Uncheck to save as a draft that recipients won&apos;t see
                        yet.
                    </p>
                </div>
            </div>

            <div className="flex items-center gap-3">
                <Button
                    type="submit"
                    disabled={processing}
                    data-test="announcement-submit"
                >
                    {processing && <Spinner />}
                    {submitLabel}
                </Button>
            </div>
        </form>
    );
}
