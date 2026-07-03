<?php

namespace App\Concerns;

use App\Data\AppPermissions;
use App\Enums\AppPermission;

trait HasAppPermissions
{
    /**
     * Get the user's application-level permissions.
     */
    public function toAppPermissions(): AppPermissions
    {
        return new AppPermissions(
            canManageUsers: $this->can(AppPermission::ManageUsers->value),
            canManageTheses: $this->can(AppPermission::ManageTheses->value),
            canViewTheses: $this->can(AppPermission::ViewTheses->value),
            canSubmitThesis: $this->can(AppPermission::SubmitThesis->value),
            canReviewThesis: $this->can(AppPermission::ReviewThesis->value),
            canManageAnnouncements: $this->can(AppPermission::ManageAnnouncements->value),
            canAccessRegistrar: $this->can(AppPermission::AccessRegistrar->value),
            canManageStudents: $this->can(AppPermission::ManageStudents->value),
            canManageSections: $this->can(AppPermission::ManageSections->value),
            canManageEnrollments: $this->can(AppPermission::ManageEnrollments->value),
            canAccessCashier: $this->can(AppPermission::AccessCashier->value),
            canManagePayments: $this->can(AppPermission::ManagePayments->value),
            canManageCashierNotifications: $this->can(AppPermission::ManageCashierNotifications->value),
        );
    }
}
