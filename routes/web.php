<?php

use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Student\StudentInformationController;
use App\Http\Controllers\Teams\TeamInvitationController;
use App\Http\Middleware\EnsureTeamMembership;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'welcome')->name('home');

Route::prefix('{current_team}')
    ->middleware(['auth', 'verified', EnsureTeamMembership::class])
    ->group(function () {
        Route::get('dashboard', DashboardController::class)->name('dashboard');
        Route::get('information-sheet', [StudentInformationController::class, 'edit'])->name('information-sheet.edit');
        Route::post('information-sheet', [StudentInformationController::class, 'store'])->name('information-sheet.store');
    });

Route::middleware(['auth'])->group(function () {
    Route::get('invitations/{invitation}/accept', [TeamInvitationController::class, 'accept'])->name('invitations.accept');
    Route::delete('invitations/{invitation}', [TeamInvitationController::class, 'decline'])->name('invitations.decline');

    Route::get('announcements', [AnnouncementController::class, 'index'])->name('announcements.index');
    Route::post('announcements/read-all', [AnnouncementController::class, 'markAllAsRead'])->name('announcements.read-all');
    Route::post('announcements/{announcement}/read', [AnnouncementController::class, 'markAsRead'])->name('announcements.read');

    Route::get('notifications', [\App\Http\Controllers\Student\StudentNotificationController::class, 'index'])->name('notifications.index');
    Route::post('notifications/read-all', [\App\Http\Controllers\Student\StudentNotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::post('notifications/{notification}/read', [\App\Http\Controllers\Student\StudentNotificationController::class, 'markAsRead'])->name('notifications.read');
});

require __DIR__.'/settings.php';
require __DIR__.'/admin.php';
require __DIR__.'/registrar.php';
require __DIR__.'/cashier.php';
