<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    public function getNotifications()
    {
        $notifications = DatabaseNotification::where('notifiable_id', Auth::id())
        ->whereNull('read_at')
        ->orderBy('created_at', 'desc') // or 'desc'
        ->get();
        //return response()->json(Auth::user()->unreadNotifications->take(5));
        return response()->json($notifications->take(5));
    }

    public function markAllRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        return response()->json(['message' => 'All notifications marked as read']);
    }
}
