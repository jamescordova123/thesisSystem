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
    ) {
        //
    }
}
