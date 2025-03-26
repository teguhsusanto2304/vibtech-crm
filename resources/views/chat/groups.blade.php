@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Chat Groups</h2>

    <form action="{{ url('/chat-groups') }}" method="POST">
        @csrf
        <input type="text" name="name" placeholder="Enter group name" required>
        <button type="submit">Create Group</button>
    </form>

    <ul>
        @foreach ($groups as $group)
            <li>
                <a href="{{ route('chat-group.messages', $group->id) }}">{{ $group->name }}</a>
            </li>
        @endforeach
    </ul>
</div>
@endsection
