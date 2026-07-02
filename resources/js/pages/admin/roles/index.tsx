import { Form, Head } from '@inertiajs/react';
import Heading from '@/components/heading';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Label } from '@/components/ui/label';
import { index, update } from '@/routes/admin/roles';
import type { AdminRole } from '@/types';

type Props = {
    roles: AdminRole[];
};

export default function RolesIndex({ roles }: Props) {
    return (
        <>
            <Head title="Roles" />

            <h1 className="sr-only">Roles</h1>

            <div className="flex flex-col space-y-10">
                <Heading
                    variant="small"
                    title="Roles"
                    description="Configure which permissions each role has"
                />

                {roles.map((role) => (
                    <div
                        key={role.value}
                        className="space-y-4 rounded-lg border p-4"
                        data-test="role-card"
                    >
                        <Heading variant="small" title={role.label} />

                        <Form
                            {...update.form(role.value)}
                            className="space-y-4"
                        >
                            {({ errors, processing }) => (
                                <>
                                    <div className="space-y-3">
                                        {role.permissions.map((permission) => (
                                            <div
                                                key={permission.value}
                                                className="flex items-center gap-3"
                                            >
                                                <Checkbox
                                                    id={`${role.value}-${permission.value}`}
                                                    name="permissions[]"
                                                    value={permission.value}
                                                    defaultChecked={
                                                        permission.assigned
                                                    }
                                                    data-test={`permission-checkbox-${role.value}-${permission.value}`}
                                                />
                                                <Label
                                                    htmlFor={`${role.value}-${permission.value}`}
                                                    className="font-normal"
                                                >
                                                    {permission.label}
                                                </Label>
                                            </div>
                                        ))}
                                    </div>

                                    <InputError message={errors.permissions} />

                                    <Button
                                        type="submit"
                                        size="sm"
                                        data-test={`role-save-button-${role.value}`}
                                        disabled={processing}
                                    >
                                        Save {role.label}
                                    </Button>
                                </>
                            )}
                        </Form>
                    </div>
                ))}
            </div>
        </>
    );
}

RolesIndex.layout = {
    breadcrumbs: [
        {
            title: 'Roles',
            href: index(),
        },
    ],
};
