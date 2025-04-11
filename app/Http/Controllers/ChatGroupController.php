<?php

namespace App\Http\Controllers;

use App\Models\ChatGroup;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use App\Notifications\UserNotification;

class ChatGroupController extends Controller
{
    public function getInvitedUsers($groupId): JsonResponse
    {
        // Get the group details
        $group = ChatGroup::findOrFail($groupId);

        $authUserId = auth()->id(); // Get logged-in user ID

        // Get all users except the logged-in user
        $users = User::whereNotIn('id', [$authUserId])
        ->where('user_status',1)
        ->get();
        // Get the IDs of users already invited to this group
        $invitedUsers = DB::table('chat_group_members')->where('chat_group_id', $groupId)
            ->pluck('user_id')
            ->toArray();

        // Format the response
        return response()->json([
            'group' => [
                'id' => $group->id,
                'name' => $group->name
            ],
            'users' => $users,
            'invited_users' => $invitedUsers
        ]);
    }
    public function index()
    {
        $userId = auth()->user()->id;
        // Fetch only groups where the user is a member
        $groups = DB::table('chat_groups')
        ->join('chat_group_members', 'chat_groups.id', '=', 'chat_group_members.chat_group_id')
        ->join('users', 'chat_group_members.user_id', '=', 'users.id') // Join with users to get names
        ->where('chat_group_members.user_id', $userId) // Only fetch groups the user is in
        ->select('chat_groups.id', 'chat_groups.name', 'chat_group_members.is_creator')
        ->get();

        return view('chat.groups', compact('groups'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $chatGroup = ChatGroup::create(['name' => $request->name]);
        DB::table('chat_group_members')->insert([
            'chat_group_id' => $chatGroup->id,
            'user_id' => auth()->user()->id,
            'is_creator'=>1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        return redirect()->route('chat-groups');
    }

    public function inviteUsers(Request $request, $groupId)
    {
        $group = ChatGroup::findOrFail($groupId);
        $newInvitedUserIds = $request->input('invited_users', []); // Get checked users (empty array if none selected)

        // Get current members of the group
        $currentMembers = DB::table('chat_group_members')
            ->where('chat_group_id', $groupId)
            ->pluck('user_id')
            ->toArray();

        // Users to remove (Unchecked)
        $usersToRemove = array_diff($currentMembers, $newInvitedUserIds);

        // Users to add (Checked but not yet in the group)
        $usersToAdd = array_diff($newInvitedUserIds, $currentMembers);

        // Remove unselected users from the group
        if (!empty($usersToRemove)) {
            DB::table('chat_group_members')
                ->where('chat_group_id', $groupId)
                ->whereIn('user_id', $usersToRemove)
                ->whereNot('is_creator',1)
                ->delete();
                // Get the users to be notified
                $members = User::whereIn('id', $usersToRemove)->get();

                // Send the notification to each removed user
                foreach ($members as $member) {
                    $member->notify(new UserNotification(
                        auth()->user()->name . ' <strong>Removed</strong> you from Chat Group ' . $group->name,
                        'danger',
                        route('chat-groups')
                    ));
                }
        }

        // Add newly checked users to the group
        foreach ($usersToAdd as $userId) {
            DB::table('chat_group_members')->insert([
                'chat_group_id' => $groupId,
                'user_id' => $userId,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $member = User::find($userId);
            $member->notify(new UserNotification(
                auth()->user()->name . ' <strong>Invited</strong> you at Chat Group' . $group->name,
                'success',
                route('chat-groups')
            ));
        }

        return back()->with('success', 'Users updated successfully!');
    }
}
