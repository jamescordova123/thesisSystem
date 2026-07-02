import { Form } from '@inertiajs/react';
import type { PropsWithChildren } from 'react';
import { useState } from 'react';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { store } from '@/routes/admin/users';
import type { AppRoleOption } from '@/types';

type Props = PropsWithChildren<{
    availableRoles: AppRoleOption[];
}>;

export default function CreateUserModal({ availableRoles, children }: Props) {
    const [open, setOpen] = useState(false);

    return (
        <Dialog open={open} onOpenChange={setOpen}>
            <DialogTrigger asChild>{children}</DialogTrigger>
            <DialogContent>
                <Form
                    key={String(open)}
                    {...store.form()}
                    className="space-y-6"
                    onSuccess={() => setOpen(false)}
                    resetOnSuccess
                >
                    {({ errors, processing }) => (
                        <>
                            <DialogHeader>
                                <DialogTitle>Create a new user</DialogTitle>
                                <DialogDescription>
                                    Add a new user account and assign their
                                    roles.
                                </DialogDescription>
                            </DialogHeader>

                            <div className="grid gap-4">
                                <div className="grid gap-2">
                                    <Label htmlFor="name">Name</Label>
                                    <Input
                                        id="name"
                                        name="name"
                                        data-test="create-user-name"
                                        placeholder="Jane Doe"
                                        required
                                    />
                                    <InputError message={errors.name} />
                                </div>

                                <div className="grid gap-2">
                                    <Label htmlFor="email">Email address</Label>
                                    <Input
                                        id="email"
                                        name="email"
                                        type="email"
                                        data-test="create-user-email"
                                        placeholder="jane@example.com"
                                        required
                                    />
                                    <InputError message={errors.email} />
                                </div>

                                <div className="grid gap-2">
                                    <Label htmlFor="password">Password</Label>
                                    <Input
                                        id="password"
                                        name="password"
                                        type="password"
                                        data-test="create-user-password"
                                        autoComplete="new-password"
                                        required
                                    />
                                    <InputError message={errors.password} />
                                </div>

                                <div className="grid gap-2">
                                    <Label htmlFor="password_confirmation">
                                        Confirm password
                                    </Label>
                                    <Input
                                        id="password_confirmation"
                                        name="password_confirmation"
                                        type="password"
                                        data-test="create-user-password-confirmation"
                                        autoComplete="new-password"
                                        required
                                    />
                                </div>

                                <div className="grid gap-2">
                                    <Label>Roles</Label>
                                    <div className="space-y-2">
                                        {availableRoles.map((role) => (
                                            <div
                                                key={role.value}
                                                className="flex items-center gap-3"
                                            >
                                                <Checkbox
                                                    id={`new-user-role-${role.value}`}
                                                    name="roles[]"
                                                    value={role.value}
                                                    data-test={`create-user-role-${role.value}`}
                                                />
                                                <Label
                                                    htmlFor={`new-user-role-${role.value}`}
                                                    className="font-normal"
                                                >
                                                    {role.label}
                                                </Label>
                                            </div>
                                        ))}
                                    </div>
                                    <InputError message={errors.roles} />
                                </div>
                            </div>

                            <DialogFooter className="gap-2">
                                <DialogClose asChild>
                                    <Button variant="secondary">Cancel</Button>
                                </DialogClose>

                                <Button
                                    type="submit"
                                    data-test="create-user-submit"
                                    disabled={processing}
                                >
                                    Create user
                                </Button>
                            </DialogFooter>
                        </>
                    )}
                </Form>
            </DialogContent>
        </Dialog>
    );
}
