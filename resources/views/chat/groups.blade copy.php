@extends('layouts.app')

@section('content')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"></script>
<div class="container">
    <h2>Chat Groups</h2>
    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ url('/chat-groups') }}" method="POST" class="form-inline">
                @csrf
                <div class="input-group">
                    <input type="text" name="name" class="form-control" placeholder="Enter group name" required>
                    <button type="submit" class="btn btn-primary">Create a Chat Group</button>

                  </div>
            </form>
            <div class="row mt-5">
                @forelse($groups as $group)
                <div class="col-md-3 mb-3">
                    <div class="card h-100">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">{{ $group->name }}</h5>
                            <div class="mt-auto d-flex justify-content-center">
                                <div class="btn-group" role="group" aria-label="Basic example">
                                    <a href="{{ route('v1.getting-started.edit',['id'=>$group->id]) }}" class="btn btn-info btn-sm">Chat</a>
                                    <a href="{{ route('v1.getting-started.edit',['id'=>$group->id]) }}" class="btn btn-primary btn-sm">Edit</a>
                                    <a href="{{ route('v1.getting-started.edit',['id'=>$group->id]) }}" class="btn btn-danger btn-sm">Delete</a>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                    <p>No chat group available.</p>
                @endforelse
            </div>
            <div class="d-flex justify-content-center">
                {!! $groups->links() !!}
            </div>
        </div>
    </div>
<script>

    async function fetchInvitedUsers(groupId, tableBodyId) {
    try {
        const response = await fetch(`/chat-groups/${groupId}/invited-users`);
        const data = await response.json();
        updateInviteModal(data.users, data.invited_users, tableBodyId);
    } catch (error) {
        console.error("Error fetching invited users:", error);
    }
}


// Function to update the invite modal
function updateInviteModal(users, invitedUsers, tableBodyId) {
    let tableBody = document.getElementById(tableBodyId);
    tableBody.innerHTML = "";

    users.forEach((user, index) => {
        let isChecked = invitedUsers.includes(user.id) ? "checked" : "";
        let row = `
            <tr>
                <td>${index + 1}</td>
                <td>${user.name}</td>
                <td>${user.position || "N/A"}</td>
                <td>
                    <input type="checkbox" class="form-check-input" name="invited_users[]" value="${user.id}" ${isChecked}>
                </td>
            </tr>
        `;
        tableBody.innerHTML += row;
    });
}


// Call function when modal opens
document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".invite-user-btn").forEach(button => {
        button.addEventListener("click", function () {
            let groupId = this.dataset.groupId;
            let modalId = `inviteUserModal${groupId}`;
            let tableBodyId = `staffTableBody${groupId}`;

            let modalElement = document.getElementById(modalId);
            let tableBody = document.getElementById(tableBodyId);

            console.log("Group ID:", groupId);
            console.log("Modal Element:", modalElement);
            console.log("Table Body:", tableBody);

            if (!modalElement || !tableBody) {
                console.error("❌ Modal or Table Body not found! Make sure the modal HTML is correctly generated.");
                return;
            }

            fetchInvitedUsers(groupId, tableBodyId);

            let modal = new bootstrap.Modal(modalElement);
            modal.show();
        });
    });
});


    </script>


<table width="100%">
    @foreach ($groups as $group)
        <tr>
            <td width="70%">{{ $group->name }}</td>
            <td width="5%"><a href="{{ route('chat-group.messages', $group->id) }}" class="btn btn-info">Chat</a>
            </td>
            @if($group->is_creator==1)
            <td width="20%">
            <button class="btn btn-outline-secondary invite-user-btn"
                data-toggle="modal"
                data-target="#inviteUserModal{{ $group->id }}"
                data-group-id="{{ $group->id }}">
                Invite User
            </button>
            <div class="modal fade" id="inviteUserModal{{ $group->id }}" tabindex="-1"
                role="dialog" aria-labelledby="inviteUserModalLabel{{ $group->id }}" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Invite User to {{ $group->name }}</h5>

                        </div>
                        <form action="{{ route('chat-groups.invite-users', $group->id) }}" method="POST">
                            @csrf
                            <div class="modal-body">
                                <table class="table table-striped" id="staffTable{{ $group->id }}">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Name</th>
                                            <th>Position</th>
                                            <th>Invite</th>
                                        </tr>
                                    </thead>
                                    <tbody id="staffTableBody{{ $group->id }}">
                                        </tbody>
                                </table>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Invite Selected Users</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            </td>
            @endif


        </tr>
    @endforeach
    </table>
</div>
@endsection
