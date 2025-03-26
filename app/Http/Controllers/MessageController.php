<?php

namespace App\Http\Controllers;

use App\Models\ChatGroup;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function index(ChatGroup $group)
    {
        $messages = $group->messages()->with('user')->get();
        return view('chat.messages', compact('group', 'messages'));
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


