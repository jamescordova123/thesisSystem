<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class AnnouncementController extends Controller
{
    /**
     * Display the announcements addressed to the current user.
     */
    public function index(Request $request): Response
    {
        $announcements = $request->user()
            ->receivedAnnouncements()
            ->with('creator')
            ->published()
            ->orderByDesc('published_at')
            ->get()
            ->map(fn (Announcement $announcement) => [
                'id' => $announcement->id,
                'title' => $announcement->title,
                'body' => $announcement->body,
                'image_url' => $announcement->image_url,
                'published_at' => $announcement->published_at?->toIso8601String(),
                'author' => $announcement->creator?->name,
                'read_at' => $announcement->pivot->read_at,
            ]);

        return Inertia::render('announcements/index', [
            'announcements' => $announcements,
        ]);
    }

    /**
     * Mark a single announcement as read for the current user.
     */
    public function markAsRead(Request $request, Announcement $announcement): RedirectResponse
    {
        $request->user()
            ->receivedAnnouncements()
            ->updateExistingPivot($announcement->id, ['read_at' => now()]);

        return back();
    }

    /**
     * Mark all of the current user's announcements as read.
     */
    public function markAllAsRead(Request $request): RedirectResponse
    {
        DB::table('announcement_user')
            ->where('user_id', $request->user()->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return back();
    }
}
