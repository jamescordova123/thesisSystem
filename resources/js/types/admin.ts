export type AppRoleValue = 'super-admin' | 'registrar' | 'cashier' | 'student';

export type AppPermissionValue =
    | 'users.manage'
    | 'theses.manage'
    | 'theses.view'
    | 'theses.submit'
    | 'theses.review'
    | 'announcements.manage'
    | 'registrar.access'
    | 'students.manage'
    | 'sections.manage'
    | 'enrollments.manage'
    | 'cashier.access'
    | 'payments.manage'
    | 'cashier.notifications';

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
    role: AppRoleOption | null;
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
    canManageAnnouncements: boolean;
    canAccessRegistrar: boolean;
    canManageStudents: boolean;
    canManageSections: boolean;
    canManageEnrollments: boolean;
    canAccessCashier: boolean;
    canManagePayments: boolean;
    canManageCashierNotifications: boolean;
};

export type AnnouncementRecipient = {
    id: number;
    name: string;
    email: string;
    read_at: string | null;
};

export type AdminAnnouncement = {
    id: number;
    title: string;
    body: string;
    image_url: string | null;
    send_to_all: boolean;
    is_published: boolean;
    published_at: string | null;
    created_at: string;
    author: string | null;
    recipients_count: number;
    read_count: number;
    recipient_ids: number[];
};

export type UserAnnouncement = {
    id: number;
    title: string;
    body: string;
    image_url: string | null;
    published_at: string | null;
    author: string | null;
    read_at: string | null;
};
