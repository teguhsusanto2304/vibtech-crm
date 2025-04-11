@extends('layouts.app')

@section('content')
<style>
    .container {
    max-width: 600px;
    margin: auto;
    padding: 20px;
}

.chat-box {
    max-height: 400px;
    overflow-y: auto;
    border: 1px solid #ddd;
    border-radius: 10px;
    padding: 10px;
    background: #f9f9f9;
}

.chat-messages {
    list-style: none;
    padding: 0;
}

.chat-message {
    display: flex;
    margin-bottom: 10px;
}

.chat-message.sent {
    justify-content: flex-end;
}

.chat-message.received {
    justify-content: flex-start;
}

.message-bubble {
    max-width: 60%;
    padding: 10px;
    border-radius: 15px;
    position: relative;
    font-size: 14px;
    color: white;
}

.sent .message-bubble {
    background: #007bff;
    border-bottom-right-radius: 0;
}

.received .message-bubble {
    background: #28a745;
    border-bottom-left-radius: 0;
}

.message-bubble strong {
    display: block;
    font-size: 12px;
    margin-bottom: 5px;
    color: #fff;
}

.chat-form {
    display: flex;
    margin-top: 10px;
}

.chat-input {
    flex: 1;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 20px;
}

.chat-send-btn {
    background: #007bff;
    color: white;
    border: none;
    padding: 10px 15px;
    border-radius: 20px;
    margin-left: 5px;
    cursor: pointer;
}

.chat-send-btn:hover {
    background: #0056b3;
}

.back-link {
    display: block;
    margin-top: 10px;
    text-align: center;
    text-decoration: none;
    color: #007bff;
}

    </style>
<div class="container">
    <h2 class="text-center mb-4">{{ $group->name }} - Chat</h2>

    <div class="chat-box">
        <ul class="chat-messages">
            @foreach ($messages as $message)
                <li class="chat-message {{ $message->user->id == auth()->id() ? 'sent' : 'received' }}">
                    <div class="message-bubble">
                        <strong>{{ $message->user->name }}</strong>
                        <p>{{ $message->message }}</p>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>

    <form action="{{ url('/chat-groups/' . $group->id . '/messages') }}" method="POST" class="chat-form">
        @csrf
        <input type="text" name="message" placeholder="Type a message..." required class="chat-input">
        <button type="submit" class="chat-send-btn">Send</button>
    </form>

    <a href="{{ route('chat-groups') }}" class="back-link">Back to Groups</a>
</div>

@endsection
