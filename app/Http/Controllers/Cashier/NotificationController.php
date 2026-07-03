<?php

namespace App\Http\Controllers\Cashier;

use App\Enums\AppRole;
use App\Enums\CashierNotificationType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cashier\StoreCashierNotificationRequest;
use App\Models\StudentNotification;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class NotificationController extends Controller
{
    public function index(Request $request): Response
    {
        $notifications = StudentNotification::query()
            ->with(['user.studentInformation', 'sender'])
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->string('search')->toString();
                $q->where(function ($query) use ($search) {
                    $query->where('subject', 'like', "%{$search}%")
                        ->orWhereHas('user', fn ($uq) => $uq
                            ->where('name', 'like', "%{$search}%")
                            ->orWhereHas('studentInformation', fn ($sq) => $sq
                                ->where('full_name', 'like', "%{$search}%")));
                });
            })
            ->latest('sent_at')
            ->paginate(15)
            ->withQueryString()
            ->through(fn (StudentNotification $n) => [
                'id' => $n->id,
                'subject' => $n->subject,
                'message' => $n->message,
                'type' => $n->type,
                'student' => $n->user->studentInformation?->full_name ?? $n->user->name,
                'student_number' => $n->user->studentInformation?->student_number,
                'sender' => $n->sender?->name,
                'sent_at' => $n->sent_at?->toIso8601String(),
                'read_at' => $n->read_at?->toIso8601String(),
                'status' => $n->isRead() ? 'read' : 'sent',
            ]);

        return Inertia::render('cashier/notifications/index', [
            'notifications' => $notifications,
            'filters' => $request->only('search'),
            'students' => User::havingAppRole(AppRole::Student)
                ->with('studentInformation')
                ->whereHas('studentInformation', fn ($q) => $q->where('is_archived', false))
                ->orderBy('name')
                ->get()
                ->map(fn (User $u) => [
                    'id' => $u->id,
                    'name' => $u->studentInformation?->full_name ?? $u->name,
                    'student_number' => $u->studentInformation?->student_number,
                ]),
            'notificationTypes' => CashierNotificationType::options(),
        ]);
    }

    public function store(StoreCashierNotificationRequest $request): RedirectResponse
    {
        $notification = StudentNotification::create([
            'user_id' => $request->integer('user_id'),
            'sender_id' => $request->user()->id,
            'type' => $request->validated('type'),
            'subject' => $request->validated('subject'),
            'message' => $request->validated('message'),
            'sent_at' => now(),
        ]);

        AuditLogger::log('cashier.notification.sent', $notification, null, $notification->toArray());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Notification sent successfully.')]);

        return to_route('cashier.notifications.index');
    }
}
