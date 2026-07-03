<?php

namespace App\Data;

readonly class AppPermissions
{
    public function __construct(
        public bool $canManageUsers,
        public bool $canManageTheses,
        public bool $canViewTheses,
        public bool $canSubmitThesis,
        public bool $canReviewThesis,
        public bool $canManageAnnouncements,
        public bool $canAccessRegistrar,
        public bool $canManageStudents,
        public bool $canManageSections,
        public bool $canManageEnrollments,
        public bool $canAccessCashier,
        public bool $canManagePayments,
        public bool $canManageCashierNotifications,
    ) {
        //
    }
}
