export type AppRoleValue = 'super-admin' | 'admin' | 'student' | 'advisor';

export type AppPermissionValue =
    | 'users.manage'
    | 'theses.manage'
    | 'theses.view'
    | 'theses.submit'
    | 'theses.review';

export type AppRoleOption = {
    value: AppRoleValue;
    label: string;
};

export type PermissionOption = {
    value: AppPermissionValue;
    label: string;
    assigned: boolean;
};

export type AdminUser = {
    id: number;
    name: string;
    email: string;
    roles: AppRoleOption[];
};

export type AdminRole = {
    value: AppRoleValue;
    label: string;
    permissions: PermissionOption[];
};

export type AppPermissions = {
    canManageUsers: boolean;
    canManageTheses: boolean;
    canViewTheses: boolean;
    canSubmitThesis: boolean;
    canReviewThesis: boolean;
};
