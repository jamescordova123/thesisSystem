import { Form, Head } from '@inertiajs/react';
import Heading from '@/components/heading';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Label } from '@/components/ui/label';
import { edit, index, update } from '@/routes/admin/users';
import type { AdminUser, AppRoleOption } from '@/types';

type Props = {
    user: AdminUser;
    availableRoles: AppRoleOption[];
};

export default function UserEdit({ user, availableRoles }: Props) {
    const assignedRoles = new Set(user.roles.map((role) => role.value));

    return (
        <>
            <Head title={`Edit ${user.name}`} />

            <h1 className="sr-only">Edit {user.name}</h1>

            <div className="flex flex-col space-y-6">
                <Heading
                    variant="small"
                    title={user.name}
                    description={user.email}
                />

                <Form {...update.form(user.id)} className="space-y-6">
                    {({ errors, processing }) => (
                        <>
                            <div className="space-y-4">
                                <Label>Roles</Label>
                                <div className="space-y-3">
                                    {availableRoles.map((role) => (
                                        <div
                                            key={role.value}
                                            className="flex items-center gap-3"
                                        >
                                            <Checkbox
                                                id={`role-${role.value}`}
                                                name="roles[]"
                                                value={role.value}
                                                defaultChecked={assignedRoles.has(
                                                    role.value,
                                                )}
                                                data-test={`role-checkbox-${role.value}`}
                                            />
                                            <Label
                                                htmlFor={`role-${role.value}`}
                                                className="font-normal"
                                            >
                                                {role.label}
                                            </Label>
                                        </div>
                                    ))}
                                </div>
                                <InputError message={errors.roles} />
                            </div>

                            <div className="flex items-center gap-4">
                                <Button
                                    type="submit"
                                    data-test="user-roles-save-button"
                                    disabled={processing}
                                >
                                    Save roles
                                </Button>
                            </div>
                        </>
                    )}
                </Form>
            </div>
        </>
    );
}

UserEdit.layout = (props: { user: AdminUser }) => ({
    breadcrumbs: [
        {
            title: 'Users',
            href: index(),
        },
        {
            title: props.user.name,
            href: edit(props.user.id),
        },
    ],
});
