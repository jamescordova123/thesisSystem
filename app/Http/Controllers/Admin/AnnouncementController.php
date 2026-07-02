<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AppRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAnnouncementRequest;
use App\Http\Requests\Admin\UpdateAnnouncementRequest;
use App\Models\Announcement;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of announcements.
     */
    public function index(): Response
    {
        $announcements = Announcement::query()
            ->with('creator')
            ->withCount([
                'recipients as recipients_count',
                'recipients as read_count' => fn ($query) => $query->whereNotNull('announcement_user.read_at'),
            ])
            ->latest()
            ->get()
            ->map(fn (Announcement $announcement) => $this->toListPayload($announcement));

        return Inertia::render('admin/announcements/index', [
            'announcements' => $announcements,
        ]);
    }

    /**
     * Show the form for creating a new announcement.
     */
    public function create(): Response
    {
        return Inertia::render('admin/announcements/create', [
            'users' => $this->userOptions(),
            'roles' => AppRole::options(),
        ]);
    }

    /**
     * Store a newly created announcement.
     */
    public function store(StoreAnnouncementRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            $announcement = Announcement::create([
                'created_by' => $request->user()->id,
                'title' => $request->validated('title'),
                'body' => $request->validated('body'),
                'image_path' => $this->storeImage($request),
                'send_to_all' => $request->boolean('send_to_all'),
                'published_at' => $request->boolean('publish') ? now() : null,
            ]);

            $announcement->recipients()->sync($this->resolveRecipientIds($request));
        });

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Announcement created.')]);

        return to_route('admin.announcements.index');
    }

    /**
     * Show the form for editing an announcement.
     */
    public function edit(Announcement $announcement): Response
    {
        $announcement->load('recipients:id');

        return Inertia::render('admin/announcements/edit', [
            'announcement' => [
                'id' => $announcement->id,
                'title' => $announcement->title,
                'body' => $announcement->body,
                'image_url' => $announcement->image_url,
                'send_to_all' => $announcement->send_to_all,
                'is_published' => $announcement->isPublished(),
                'recipient_ids' => $announcement->recipients->pluck('id')->all(),
            ],
            'users' => $this->userOptions(),
            'roles' => AppRole::options(),
        ]);
    }

    /**
     * Update the specified announcement.
     */
    public function update(UpdateAnnouncementRequest $request, Announcement $announcement): RedirectResponse
    {
        DB::transaction(function () use ($request, $announcement) {
            $imagePath = $announcement->image_path;

            if ($request->boolean('remove_image') || $request->hasFile('image')) {
                if ($imagePath) {
                    Storage::disk('public')->delete($imagePath);
                }

                $imagePath = null;
            }

            if ($request->hasFile('image')) {
                $imagePath = $this->storeImage($request);
            }

            $announcement->update([
                'title' => $request->validated('title'),
                'body' => $request->validated('body'),
                'image_path' => $imagePath,
                'send_to_all' => $request->boolean('send_to_all'),
                'published_at' => $request->boolean('publish')
                    ? ($announcement->published_at ?? now())
                    : null,
            ]);

            $announcement->recipients()->syncWithPivotValues(
                $this->resolveRecipientIds($request),
                [],
                detaching: true,
            );
        });

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Announcement updated.')]);

        return to_route('admin.announcements.index');
    }

    /**
     * Remove the specified announcement.
     */
    public function destroy(Announcement $announcement): RedirectResponse
    {
        if ($announcement->image_path) {
            Storage::disk('public')->delete($announcement->image_path);
        }

        $announcement->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Announcement deleted.')]);

        return to_route('admin.announcements.index');
    }

    /**
     * Store the uploaded banner image and return its path.
     */
    private function storeImage(Request $request): ?string
    {
        if (! $request->hasFile('image')) {
            return null;
        }

        return $request->file('image')->store('announcements', 'public');
    }

    /**
     * Resolve the target user ids based on the request selection.
     *
     * @return array<int, int>
     */
    private function resolveRecipientIds(Request $request): array
    {
        if ($request->boolean('send_to_all')) {
            return User::query()->pluck('id')->all();
        }

        $userIds = collect($request->input('user_ids', []))
            ->map(fn ($id) => (int) $id);

        $roleIds = $request->input('role_ids', []);

        if (! empty($roleIds)) {
            $userIds = $userIds->merge(
                User::query()->role($roleIds)->pluck('id')
            );
        }

        return $userIds->unique()->values()->all();
    }

    /**
     * Build the payload used in the announcements listing.
     *
     * @return array<string, mixed>
     */
    private function toListPayload(Announcement $announcement): array
    {
        return [
            'id' => $announcement->id,
            'title' => $announcement->title,
            'body' => $announcement->body,
            'image_url' => $announcement->image_url,
            'send_to_all' => $announcement->send_to_all,
            'is_published' => $announcement->isPublished(),
            'published_at' => $announcement->published_at?->toIso8601String(),
            'created_at' => $announcement->created_at?->toIso8601String(),
            'author' => $announcement->creator?->name,
            'recipients_count' => (int) $announcement->recipients_count,
            'read_count' => (int) $announcement->read_count,
        ];
    }

    /**
     * Build the selectable user options.
     *
     * @return array<int, array<string, mixed>>
     */
    private function userOptions(): array
    {
        return User::query()
            ->orderBy('name')
            ->get(['id', 'name', 'email'])
            ->map(fn (User $user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ])
            ->all();
    }
}
