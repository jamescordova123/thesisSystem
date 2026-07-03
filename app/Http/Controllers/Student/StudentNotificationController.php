<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\StudentNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StudentNotificationController extends Controller
{
    public function index(Request $request): Response
    {
        $notifications = StudentNotification::query()
            ->where('user_id', $request->user()->id)
            ->with('sender')
            ->latest('sent_at')
            ->paginate(15)
            ->through(fn (StudentNotification $n) => [
                'id' => $n->id,
                'subject' => $n->subject,
                'message' => $n->message,
                'type' => $n->type,
                'sender' => $n->sender?->name,
                'sent_at' => $n->sent_at?->toIso8601String(),
                'read_at' => $n->read_at?->toIso8601String(),
                'status' => $n->isRead() ? 'read' : 'sent',
            ]);

        return Inertia::render('student/notifications/index', [
            'notifications' => $notifications,
        ]);
    }

    public function markAsRead(Request $request, StudentNotification $notification): RedirectResponse
    {
        abort_unless($notification->user_id === $request->user()->id, 403);

        $notification->update(['read_at' => now()]);

        return back();
    }

    public function markAllAsRead(Request $request): RedirectResponse
    {
        StudentNotification::query()
            ->where('user_id', $request->user()->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return back();
    }
}
