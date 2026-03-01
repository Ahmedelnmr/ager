<?php

namespace App\Services;

use App\Models\AppNotification;
use App\Models\User;

class NotificationService
{
    /**
     * Create a notification for all users with given roles.
     */
    public function notifyRoles(array $roles, string $type, array $payload): void
    {
        $users = User::role($roles)->get();
        foreach ($users as $user) {
            $this->notifyUser($user->id, $type, $payload);
        }
    }

    /**
     * Create a notification for a specific user.
     */
    public function notifyUser(int $userId, string $type, array $payload): AppNotification
    {
        return AppNotification::create([
            'user_id' => $userId,
            'type'    => $type,
            'payload' => $payload,
            'is_read' => false,
        ]);
    }

    /**
     * Get unread count for a user.
     */
    public function unreadCount(int $userId): int
    {
        return AppNotification::where('user_id', $userId)
            ->where('is_read', false)
            ->count();
    }

    /**
     * Mark all notifications as read for a user.
     */
    public function markAllRead(int $userId): void
    {
        AppNotification::where('user_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);
    }
}
