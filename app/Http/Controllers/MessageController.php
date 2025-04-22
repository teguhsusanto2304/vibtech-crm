<?php

namespace App\Http\Controllers;

use App\Models\ChatGroup;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    public function index(ChatGroup $group)
    {
        $user = Auth::user();

        // Fetch messages
        $messages = $group->messages()->with('user')->get();

        // Mark all unread messages as read for the current user
        foreach ($messages as $message) {
            if (!$message->readers->contains($user->id)) {
                $message->readers()->attach($user->id, ['read_at' => now()]);
            }
        }
        $members = DB::table('chat_group_members')
        ->join('users', 'chat_group_members.user_id', '=', 'users.id')
        ->where('chat_group_members.chat_group_id', $group->id)
        ->select('users.id', 'users.name')
        ->get(); // Fetch group members
        return view('chat.messages', compact('group', 'messages','members'));
    }

    public function store(Request $request, ChatGroup $group)
    {
        $request->validate(['message' => 'required|string']);

        $message = Message::create([
            'chat_group_id' => $group->id,
            'user_id' => Auth::id(),
            'message' => $request->message,
        ]);

        // Get all other members in the group (exclude sender)
        $otherMembers = DB::table('chat_group_members')
        ->where('chat_group_id', $group->id)
        ->where('user_id', '!=', Auth::id())
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

        return redirect()->route('chat-group.messages', $group->id);
    }
}


