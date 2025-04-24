<?php

namespace App\Http\Controllers;

use App\Models\ChatGroup;
use App\Models\Message;
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
            ->where('user_status', 1)
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
        /**$groups1 = DB::table('chat_groups')
            ->join('chat_group_members', 'chat_groups.id', '=', 'chat_group_members.chat_group_id')
            ->join('users', 'chat_group_members.user_id', '=', 'users.id') // Join with users to get names
            ->where('chat_group_members.user_id', $userId) // Only fetch groups the user is in
            ->where('chat_groups.data_status', 1)
            ->select('chat_groups.id', 'chat_groups.name', 'chat_group_members.is_creator')
            ->latest('chat_groups.created_at')
            ->paginate(20);
            **/

            $groups = DB::table('chat_groups')
            ->join('chat_group_members', 'chat_groups.id', '=', 'chat_group_members.chat_group_id')
            ->join('users', 'chat_group_members.user_id', '=', 'users.id')
            ->leftJoin('messages', 'chat_groups.id', '=', 'messages.chat_group_id')
            ->leftJoin('message_reads', function ($join) use ($userId) {
                $join->on('messages.id', '=', 'message_reads.message_id')
                     ->where('message_reads.user_id', '=', $userId)
                     ->whereNull('message_reads.read_at');
            })
            ->where('chat_group_members.user_id', $userId)
            ->where('chat_groups.data_status', 1)
            ->select(
                'chat_groups.id',
                'chat_groups.name',
                'chat_group_members.is_creator',
                DB::raw('COUNT(message_reads.id) as unread_count')
            )
            ->groupBy('chat_groups.id', 'chat_groups.name', 'chat_group_members.is_creator')
            ->orderByDesc('chat_groups.created_at')
            ->paginate(20);


            $del_groups = DB::table('chat_groups')
            ->join('chat_group_members', 'chat_groups.id', '=', 'chat_group_members.chat_group_id')
            ->join('users', 'chat_group_members.user_id', '=', 'users.id') // Join with users to get names
            ->where('chat_group_members.user_id', $userId) // Only fetch groups the user is in
            ->where('chat_group_members.is_creator', 1)
            ->where('chat_groups.data_status', 0)
            ->select('chat_groups.id', 'chat_groups.name', 'chat_group_members.is_creator')
            ->latest('chat_groups.created_at')
            ->paginate(20);


        return view('chat.groups', compact('groups','del_groups'))->with('title', 'Chat Group')->with('breadcrumb', ['Home', 'Chat', 'Chat Group']);
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $chatGroup = ChatGroup::create(['name' => $request->name]);
        DB::table('chat_group_members')->insert([
            'chat_group_id' => $chatGroup->id,
            'user_id' => auth()->user()->id,
            'is_creator' => 1,
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
                ->whereNot('is_creator', 1)
                ->delete();
            // Get the users to be notified
            $members = User::whereIn('id', $usersToRemove)
                ->get();

            // Send the notification to each removed user
            foreach ($members as $member) {
                if (auth()->user()->name != $member->name) {
                    $member->notify(new UserNotification(
                        auth()->user()->name . ' <strong>Removed</strong> you from Chat Group ' . $group->name,
                        'danger',
                        route('chat-groups')
                    ));
                }
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
                auth()->user()->name . ' <strong>Invited</strong> you at Chat Group ' . $group->name,
                'success',
                route('chat-groups')
            ));
        }

        return back()->with('success', 'Users updated successfully!');
    }

    public function getMembers1($groupId)
    {
        $group = ChatGroup::with(['users', 'members'])->findOrFail($groupId); // assuming 'members' is the relation
        return response()->json($group->users);
    }

    public function getMembers($groupId)
    {
        $data = DB::table('chat_group_members')
            ->where('chat_group_members.chat_group_id', $groupId)
            ->join('users', 'chat_group_members.user_id', '=', 'users.id')
            ->join('chat_groups', 'chat_group_members.chat_group_id', '=', 'chat_groups.id')
            ->select(
                'users.name as name',
                'chat_group_members.chat_group_id',
                'chat_group_members.user_id',
                'chat_group_members.is_creator as is_creator'
            )
            ->get();
        return response()->json($data);
    }

    public function getMessages($id)
    {
        $group = ChatGroup::findOrFail($id);
        // Adjust 'messages' to match your actual relationship/method name
        $messages = $group->messages()->with('user')->orderBy('created_at')->get();
        foreach($messages as $message){
            DB::table('message_reads')
            ->where('message_id', $message->id)
            ->where('user_id', auth()->user()->id)
            ->update(['read_at' => now()]);
        }
        return response()->json($messages);
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'group_id' => 'required|exists:chat_groups,id',
        ]);

        $message = Message::create([
            'chat_group_id' => $request->group_id,
            'user_id' => auth()->id(),
            'message' => $request->message,
        ]);

        // Get all other members in the group (exclude sender)
        $otherMembers = DB::table('chat_group_members')
        ->where('chat_group_id', $request->group_id)
        ->where('user_id', '!=', auth()->user()->id)
        ->pluck('user_id');

        // Insert entries into message_reads for each member
        $reads = [];
        foreach ($otherMembers as $userId) {
            $reads[] = [
                'message_id' => $message->id,
                'user_id' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
                'read_at' => null // Unread by default
            ];
        }

        DB::table('message_reads')->insert($reads);

        return response()->json([
            'sender_name' => auth()->user()->name,
            'message' => $message->message,
            'timestamp' => $message->created_at->toDateTimeString(),
        ]);
    }


    public function edit($id)
    {
        $data = ChatGroup::findOrFail($id);
        return response()->json($data);
    }

    public function destroy($id,$type)
    {
        $group = ChatGroup::findOrFail($id);
        if($type=='archive'){
            $group->update(['data_status' => 0]);
        } else if($type=='restore'){
            $group->update(['data_status' => 1]);
        } else {
            $group->update(['data_status' => 3]);
        }
        // Adjust 'messages' to match your actual relationship/method name
        $messages = $group->messages()->with('user')->orderBy('created_at')->get();
        foreach($messages as $message){
            DB::table('message_reads')
            ->where('message_id', $message->id)
            ->where('user_id', auth()->user()->id)
            ->update(['read_at' => now()]);
        }

        return redirect()->back()->with('success', 'Group destroyed successfully.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $group = ChatGroup::findOrFail($id);



        $group->update(['name' => $request->name]);

        return redirect()->back()->with('success', 'Group updated successfully.');
    }

    public function unreadMessageCounts()
    {
        $userId = auth()->id();

        $groups = DB::table('chat_group_members')
            ->join('chat_groups', 'chat_groups.id', '=', 'chat_group_members.chat_group_id')
            ->where('chat_group_members.user_id', $userId)
            ->select('chat_groups.id', 'chat_groups.name')
            ->get();

        $counts = [];

        foreach ($groups as $group) {
            $unreadCount = Message::where('chat_group_id', $group->id)
                ->whereDoesntHave('readers', function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                })->count();

            $counts[] = [
                'group_id' => $group->id,
                'group_name' => $group->name,
                'unread_count' => $unreadCount,
            ];
        }

        return response()->json($counts);
    }

    public function removeMember($groupId, $userId)
    {
        $currentUser = auth()->id();

        // Ensure only the creator can remove others
        $isCreator = DB::table('chat_group_members')
            ->where('chat_group_id', $groupId)
            ->where('user_id', $currentUser)
            ->value('is_creator');

        if ($isCreator==1) {
            return response()->json(['message' => 'Forbidden '], 403);
        }

        // Prevent creator from removing themselves
        //if ($userId == $currentUser) {
            //return response()->json(['message' => 'Cannot remove yourself'], 400);
        //}

        DB::table('chat_group_members')
            ->where('chat_group_id', $groupId)
            ->where('user_id', $userId)
            ->delete();

        $member = User::find($userId);
        $group = ChatGroup::find($groupId);
        $member->notify(new UserNotification(
                auth()->user()->name . ' has <strong>Removed</strong> you from Chat Group ' . $group->name,
                'danger',
                route('chat-groups')
            ));

        return response()->json(['message' => 'Member removed']);
    }



}

