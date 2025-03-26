<?php

namespace App\Http\Controllers;

use App\Models\ChatGroup;
use Illuminate\Http\Request;

class ChatGroupController extends Controller
{
    public function index()
    {
        $groups = ChatGroup::all();
        return view('chat.groups', compact('groups'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        ChatGroup::create(['name' => $request->name]);
        return redirect()->route('chat-groups');
    }
}
