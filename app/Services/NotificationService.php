<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon; // Ensure Carbon is imported if you use now()
use App\Models\User;

class NotificationService
{
    public function getCountNotification($url)
    {
        return DB::table('notifications')->where('type', 'App\Notifications\UserNotification')
            ->where('notifiable_type', 'App\Models\User')
            ->whereRaw("JSON_UNQUOTE(data->'$.url') LIKE ?", ['%'.$url.'%'])
            ->whereNull('read_at')
            ->where('notifiable_id', auth()->user()->id)
            ->count();
    }

    public function getCountNotificationWithPermission($permission,$url)
    {
        $result = null;
        if (User::permission($permission)) {
            $result = DB::table('notifications')->where('type', 'App\Notifications\UserNotification')
                ->where('notifiable_type', 'App\Models\User')
                ->whereRaw("JSON_UNQUOTE(data->'$.url') LIKE ?", ['%'.$url.'%'])
                ->whereNull('read_at')
                ->where('notifiable_id', auth()->user()->id)
                ->count();
        }
        return $result;
    }
    /**
     * Marks specific client-database list notifications as read for the authenticated user.
     *
     * @return int The number of notifications marked as read.
     */
    public function markClientListNotificationsAsRead($url): int
    {
        // Ensure a user is authenticated before attempting to mark notifications.
        if (!Auth::check()) {
            return 0;
        }

        $userId = Auth::id();

        // Fetch unread notifications related to the client database list page for the current user
        $notificationsToMarkAsRead = DB::table('notifications')
            ->where('type', 'App\Notifications\UserNotification')
            ->where('notifiable_type', 'App\Models\User') // Assuming notifications are for User models
            ->where('notifiable_id', $userId) // Filter by authenticated user's ID
            ->whereNull('read_at') // Only unread notifications
            ->whereRaw("JSON_UNQUOTE(data->'$.url') LIKE ?", ['%'.$url.'%']) // Match the URL
            ->get();

        $markedCount = 0;

        if ($notificationsToMarkAsRead->isNotEmpty()) {
            $notificationIds = $notificationsToMarkAsRead->pluck('id')->toArray();

            // Update the 'read_at' timestamp for these notifications
            $markedCount = DB::table('notifications')
                ->whereIn('id', $notificationIds)
                ->update(['read_at' => Carbon::now()]); // Use Carbon::now() for consistency
        }

        return $markedCount;
    }

    // You can add other notification-related methods here (e.g., markAllAsRead, getUnreadCount, etc.)
}