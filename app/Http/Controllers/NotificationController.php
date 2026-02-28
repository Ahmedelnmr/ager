<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function __construct(protected NotificationService $notificationService) {}

    public function index()
    {
        $notifications = AppNotification::where('user_id', Auth::id())
            ->latest()
            ->paginate(30);
        // Mark all as read
        $this->notificationService->markAllRead(Auth::id());
        return view('notifications.index', compact('notifications'));
    }

    public function markRead(AppNotification $notification)
    {
        abort_unless($notification->user_id === Auth::id(), 403);
        $notification->markAsRead();
        return back();
    }

    public function markAllRead()
    {
        $this->notificationService->markAllRead(Auth::id());
        return back()->with('success', 'تم تحديد الكل كمقروء.');
    }
}
