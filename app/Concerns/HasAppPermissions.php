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
        );
    }
}
