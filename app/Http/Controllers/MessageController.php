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
        $messages = $group->messages()->with('user')->get();
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

        Message::create([
            'chat_group_id' => $group->id,
            'user_id' => Auth::id(),
            'message' => $request->message,
        ]);

        return redirect()->route('chat-group.messages', $group->id);
    }
}


