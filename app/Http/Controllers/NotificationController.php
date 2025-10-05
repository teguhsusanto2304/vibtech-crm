<?php

namespace App\Http\Controllers;

use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function getNotifications()
    {
        $notifications = DatabaseNotification::where('notifiable_id', Auth::id())
            ->whereNull('read_at')
            ->orderBy('created_at', 'desc') // or 'desc'
            ->get();

        // return response()->json(Auth::user()->unreadNotifications->take(5));
        return response()->json($notifications->take(5));
    }

    public function getSpecificallyNotifications()
    {
       $notifications = DatabaseNotification::where('notifiable_id', Auth::id())
        ->where(function ($query) {
            $query->where('data', 'LIKE', '%"message":"%management-memo%"%')
                ->orWhere('data', 'LIKE', '%"message":"%employee-handbooks%"%')
                ->orWhere('data', 'LIKE', '%"message":"%job-assignment-form%"%')
                ->orWhere('data', 'LIKE', '%"message":"%submit-claims%"%');
        })
        ->whereNull('read_at')
        ->orderBy('created_at', 'desc')
        ->get();

        // return response()->json(Auth::user()->unreadNotifications->take(5));
        return response()->json($notifications);
    }

    public function markAllRead()
    {
        Auth::user()->unreadNotifications->markAsRead();

        return response()->json(['message' => 'All notifications marked as read']);
    }
}
