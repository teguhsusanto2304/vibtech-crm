<?php

namespace App\Http\Controllers;

use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class NotificationController extends Controller
{
    private $categoryMap = [
        'management-memo'       => ['group' => 'Management Memo', 'colorClass' => 'border-start-success', 'bgClass' => 'bg-success-subtle'],
        'employee-handbooks'    => ['group' => 'Employee Handbook', 'colorClass' => 'border-start-danger', 'bgClass' => 'bg-danger-subtle'],
        'success'   => ['group' => 'Job Requisition', 'colorClass' => 'border-start-primary', 'bgClass' => 'bg-primary-subtle'],
        'submit-claims'         => ['group' => 'Submit Claim', 'colorClass' => 'border-start-warning', 'bgClass' => 'bg-warning-subtle'],
        // Add a default category for anything else, or use a more specific filter
        'staff-resource'        => ['group' => 'Staff Resource', 'colorClass' => 'border-start-info', 'bgClass' => 'bg-info-subtle'],
    ];
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

    public function getGroupedNotifications()
    {
        // 1. Fetch all relevant notifications
        // Note: The 'data' column is a JSON string, so we must rely on LIKE queries for filtering, 
        // which is generally inefficient. It's better to add a 'category' column if possible.
        $notifications = DatabaseNotification::where('notifiable_id', Auth::id())
            ->where(function ($query) {
                // Combine the group filters based on the keys in $this->categoryMap
                $query->where('data', 'LIKE', '%"message":"%management-memo%"%')
                ->orWhere('data', 'LIKE', '%"message":"%employee-handbooks%"%')
                ->orWhere('data', 'LIKE', '%"message":"%job-assignment-form%"%')
                ->orWhere('data', 'LIKE', '%"message":"%submit-claims%"%');
                //foreach (array_keys($this->categoryMap) as $key) {
                 //   //->orWhere('data', 'LIKE', '%"message":"%' . $key . '%"');
                //}
            })
            // Fetch both read and unread to build the complete list for the group
            ->orderBy('created_at', 'desc')
            ->get();

            //dd($notifications);
        
        // 2. Map and Group the Notifications
        // First, extract the category key for grouping and prepare the item structure
        $groupedNotifications = $notifications
    ->map(function ($notification) {
        
        // --- 1. Identify the Category Key ---
        $categoryKey = null;
        // The data field is already cast to an array/object by Laravel
        $notificationData = $notification->data; 

        // Since the 'type' field often matches your category key, 
        // using it is more reliable than string searching.
        if (isset($notificationData['type'])) {
            $categoryKey = $notificationData['type'];
        } else {
            // Fallback to string search if 'type' isn't explicitly defined/cast
            foreach (array_keys($this->categoryMap) as $key) {
                if (str_contains($notification->data, $key)) {
                    $categoryKey = $key;
                    break;
                }
            }
        }
        
        // Fallback or error check (important!)
        if (!$categoryKey || !isset($this->categoryMap[$categoryKey])) {
            $categoryKey = 'others'; // Ensure a valid key is set, even if a default
        }

        // --- 2. Extract the URL (The Answer) ---
        $referUrl = $notificationData['url'] ?? null; // Access the 'url' key directly

        // --- 3. Prepare the Item Structure ---
        return [
            'category_key' => $categoryKey,
            'item' => [
                'id'       => $notification->id,
                // Accessing the title/message field directly
                'title'    => $notificationData['message'] ?? 'Notification',
                'isRead'   => (bool)$notification->read_at, 
                'time'     => Carbon::parse($notification->created_at)->diffForHumans(),
                // Add the refer URL to your frontend item structure
                'url'      => $referUrl, 
            ]
        ];
    })
            // Group the mapped items by their extracted category key
            ->groupBy('category_key');


        // 3. Final Format Transformation
        $notificationsData = collect($this->categoryMap)->map(function ($groupData, $key) use ($groupedNotifications) {
            // Get all items belonging to this group key
            $items = $groupedNotifications->get($key, collect())
                // Extract just the 'item' array from the mapped structure
                ->pluck('item') 
                ->values();
                
            // Combine the group metadata with the list of items
            return array_merge($groupData, ['items' => $items->toArray()]);
        })
        // Remove any groups that ended up with no notifications
        ->filter(fn($group) => count($group['items']) > 0)
        ->values();

        // Return the final formatted JSON response
        return response()->json($notificationsData);
    }

    public function markAllRead()
    {
        Auth::user()->unreadNotifications->markAsRead();

        return response()->json(['message' => 'All notifications marked as read']);
    }
}
