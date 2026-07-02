<?php

namespace App\Enums;

enum AppPermission: string
{
    case ManageUsers = 'users.manage';
    case ManageTheses = 'theses.manage';
    case ViewTheses = 'theses.view';
    case SubmitThesis = 'theses.submit';
    case ReviewThesis = 'theses.review';
    case ManageAnnouncements = 'announcements.manage';

    /**
     * Get the display label for the permission.
     */
    public function label(): string
    {
        return match ($this) {
            self::ManageUsers => 'Manage users',
            self::ManageTheses => 'Manage theses',
            self::ViewTheses => 'View theses',
            self::SubmitThesis => 'Submit thesis',
            self::ReviewThesis => 'Review thesis',
            self::ManageAnnouncements => 'Manage announcements',
        };
    }

    /**
     * @return array<array{value: string, label: string}>
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->map(fn (self $permission) => ['value' => $permission->value, 'label' => $permission->label()])
            ->values()
            ->all();
    }
}
