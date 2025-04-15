@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"></script>
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <!-- custom-icon Breadcrumb-->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-custom-icon">
                    @foreach ($breadcrumb as $item)
                        <li class="breadcrumb-item">
                            <a href="javascript:void(0);">{{ $item }}</a>
                            <i class="breadcrumb-icon icon-base bx bx-chevron-right align-middle"></i>
                        </li>
                    @endforeach
                </ol>
            </nav>

            <h3>{{ $title }}</h3>

            <style>
                .user {
                    padding: 8px;
                    border-bottom: 1px solid #eee;
                    cursor: pointer;
                }

                .user:hover {
                    background: #f0f0f0;
                }

                .chat-container {
                    width: 100%;
                    background: white;
                    padding: 15px;
                    border-radius: 10px;
                    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
                }

                .chat-box {
                    max-height: 300px;
                    overflow-y: auto;
                    padding: 10px;
                    border: 1px solid #ddd;
                    border-radius: 10px;
                    background: #fff;
                }

                .message {
                    display: flex;
                    margin-bottom: 10px;
                }

                .message .bubble {
                    padding: 10px;
                    border-radius: 10px;
                    max-width: 90%;
                    word-wrap: break-word;
                }

                .sent {
                    justify-content: flex-end;
                }

                .sent .bubble {
                    background: #007bff;
                    color: white;
                    border-top-right-radius: 0;
                }

                .received {
                    justify-content: flex-start;
                }

                .received .bubble {
                    background: #e4e6eb;
                    color: black;
                    border-top-left-radius: 0;
                }

                .input-container {
                    display: flex;
                    margin-top: 10px;
                }

                .input-container input {
                    flex: 1;
                    padding: 8px;
                    border: 1px solid #ddd;
                    border-radius: 5px;
                }

                .input-container button {
                    margin-left: 5px;
                    padding: 8px 12px;
                    border: none;
                    background: #007bff;
                    color: white;
                    border-radius: 5px;
                    cursor: pointer;
                }

                .input-container button:hover {
                    background: #0056b3;
                }

                .message-container {
                    display: flex;
                    flex-direction: column;
                    max-width: 80%;
                    position: relative;
                    padding: 5px 10px;
                }

                .message-bubble {
                    padding: 10px 15px;
                    border-radius: 20px;
                    word-wrap: break-word;
                    display: inline-block;
                    max-width: 100%;
                }

                .sent .message-bubble {
                    background-color: #007bff;
                    color: white;
                    border-top-right-radius: 0;
                    align-self: flex-end;
                }

                .received .message-bubble {
                    background-color: #e4e6eb;
                    color: black;
                    border-top-left-radius: 0;
                    align-self: flex-start;
                }

                .message-timestamp {
                    font-size: 12px;
                    color: gray;
                    margin-top: 3px;
                    text-align: right;
                }
            </style>
            <div class="container">
                <!-- Online Users Sidebar -->
                <div class="row">
                    <div class="col-3">
                        <div class="card">
                            <div class="card-header text-bg-success">
                                <strong>Users</strong>
                            </div>
                            <div class="card-body">
                                <ul id="member-list" style="list-style: none; padding: 0;">
                                </ul>
                            </div>
                        </div>
                        <div class="card mt-4">
                            <div class="card-header text-bg-primary">
                                <strong>Chat Group</strong>
                            </div>
                            <div class="card-body">
                                @forelse($groups as $group)
                                    <div class="modal fade" id="inviteUserModal{{ $group->id }}" tabindex="-1" role="dialog"
                                        aria-labelledby="inviteUserModalLabel{{ $group->id }}" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Invite User to {{ $group->name }}</h5>

                                                </div>
                                                <form action="{{ route('chat-groups.invite-users', $group->id) }}"
                                                    method="POST">
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
                                                        <button type="submit" class="btn btn-primary">Invite Selected
                                                            Users</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card h-100 mb-5 group-card" data-group-id="{{ $group->id }}"
                                        data-group-name="{{ $group->name }}">
                                        <div class="card-body d-flex flex-column user">
                                            <strong class="card-title">{{ $group->name }}</strong>
                                            @if($group->is_creator == 1)
                                                <div class="mt-auto d-flex justify-content-center">
                                                    <div class="btn-group btn-group-sm" style="justify-content: center;"
                                                        role="group" aria-label="Basic example">
                                                        <button data-toggle="modal"
                                                            data-target="#editChatGroupModal{{ $group->id }}"
                                                            class="btn btn-primary btn-sm">
                                                            <svg width="17px" height="17px" viewBox="0 0 24 24" fill="none"
                                                                xmlns="http://www.w3.org/2000/svg">
                                                                <path
                                                                    d="M21.2799 6.40005L11.7399 15.94C10.7899 16.89 7.96987 17.33 7.33987 16.7C6.70987 16.07 7.13987 13.25 8.08987 12.3L17.6399 2.75002C17.8754 2.49308 18.1605 2.28654 18.4781 2.14284C18.7956 1.99914 19.139 1.92124 19.4875 1.9139C19.8359 1.90657 20.1823 1.96991 20.5056 2.10012C20.8289 2.23033 21.1225 2.42473 21.3686 2.67153C21.6147 2.91833 21.8083 3.21243 21.9376 3.53609C22.0669 3.85976 22.1294 4.20626 22.1211 4.55471C22.1128 4.90316 22.0339 5.24635 21.8894 5.5635C21.7448 5.88065 21.5375 6.16524 21.2799 6.40005V6.40005Z"
                                                                    stroke="#FFFFFF" stroke-width="1.5" stroke-linecap="round"
                                                                    stroke-linejoin="round" />
                                                                <path
                                                                    d="M11 4H6C4.93913 4 3.92178 4.42142 3.17163 5.17157C2.42149 5.92172 2 6.93913 2 8V18C2 19.0609 2.42149 20.0783 3.17163 20.8284C3.92178 21.5786 4.93913 22 6 22H17C19.21 22 20 20.2 20 18V13"
                                                                    stroke="#FFFFFF" stroke-width="1.5" stroke-linecap="round"
                                                                    stroke-linejoin="round" />
                                                            </svg></button>
                                                            <!-- Edit Group Modal -->
                                                            <div class="modal fade" id="editChatGroupModal{{ $group->id }}" tabindex="-1" role="dialog" aria-labelledby="editGroupLabel{{ $group->id }}" aria-hidden="true">
                                                                <div class="modal-dialog" role="document">
                                                                    <form action="{{ route('chat-groups.update', $group->id) }}" method="POST">
                                                                        @csrf
                                                                        @method('PUT')

                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <h5 class="modal-title" id="editGroupLabel{{ $group->id }}">Edit Chat Group</h5>

                                                                            </div>

                                                                            <div class="modal-body">
                                                                                <div class="form-group">
                                                                                    <label for="groupName{{ $group->id }}">Group Name</label>
                                                                                    <input
                                                                                        type="text"
                                                                                        class="form-control"
                                                                                        id="groupName{{ $group->id }}"
                                                                                        name="name"
                                                                                        value="{{ $group->name }}"
                                                                                        required>
                                                                                </div>
                                                                            </div>

                                                                            <div class="modal-footer">
                                                                                <button type="submit" class="btn btn-success">Save Changes</button>&nbsp;
                                                                                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                                                                            </div>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>

                                                        <a href="#"
                                                            class="btn btn-danger btn-sm"
                                                            data-toggle="modal"
                                                            data-target="#confirmDeleteModal{{ $group->id }}">
                                                            <svg width="20px" height="20px" viewBox="0 0 24 24" fill="none"
                                                                xmlns="http://www.w3.org/2000/svg">
                                                                <path
                                                                    d="M4 6H20M16 6L15.7294 5.18807C15.4671 4.40125 15.3359 4.00784 15.0927 3.71698C14.8779 3.46013 14.6021 3.26132 14.2905 3.13878C13.9376 3 13.523 3 12.6936 3H11.3064C10.477 3 10.0624 3 9.70951 3.13878C9.39792 3.26132 9.12208 3.46013 8.90729 3.71698C8.66405 4.00784 8.53292 4.40125 8.27064 5.18807L8 6M18 6V16.2C18 17.8802 18 18.7202 17.673 19.362C17.3854 19.9265 16.9265 20.3854 16.362 20.673C15.7202 21 14.8802 21 13.2 21H10.8C9.11984 21 8.27976 21 7.63803 20.673C7.07354 20.3854 6.6146 19.9265 6.32698 19.362C6 18.7202 6 17.8802 6 16.2V6M14 10V17M10 10V17"
                                                                    stroke="#FFFFFF" stroke-width="2" stroke-linecap="round"
                                                                    stroke-linejoin="round" />
                                                            </svg>
                                                        </a>
                                                        <!-- Confirm Delete Modal -->
<div class="modal fade" id="confirmDeleteModal{{ $group->id }}" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteLabel{{ $group->id }}" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <form method="POST" action="{{ route('chat-groups.destroy', $group->id) }}">
          @csrf
          @method('PUT') <!-- Or use PUT if you're "soft deleting" or marking status -->
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="confirmDeleteLabel{{ $group->id }}">Confirm Deletion</h5>

            </div>
            <div class="modal-body">
              Are you sure you want to delete <strong>{{ $group->name }}</strong>?
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>&nbsp;
              <button type="submit" class="btn btn-danger">Yes, Delete</button>
            </div>
          </div>
      </form>
    </div>
  </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <tr>
                                        <td>No chat group available.</td>
                                    </tr>
                                @endforelse
                                <div class="card h-100 mb-5 group-card">
                                    <div class="card-body d-flex flex-column user">
                                        <button class="btn btn-secondary btn-sm" data-toggle="modal" data-target="#createChatGroupModal">
                                            Create a Chat Group
                                        </button>
                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>
                    <div class="col-9">
                        <div class="card">
                            <div class="card-header text-bg-light">
                                <div class="invisible" style="display: none;">
                                    <label for="recipient">To:</label>
                                    <input type="text" id="recipient" placeholder="Enter recipient ID">
                                    <button onclick="loadPreviousMessages()">Load Chat</button>
                                </div>
                                <strong id="recipientLabel"></strong>
                            </div>
                            <div class="card-body mt-4">
                                <div class="chat-box" id="chat-box"></div>
                                <div class="input-container">
                                    <input type="text" id="message-input" placeholder="Type a message">
                                    <input type="hidden" id="group-id" >
                                    <button class="btn btn-primary" id="send-button">Send</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal -->
<div class="modal fade" id="createChatGroupModal" tabindex="-1" role="dialog" aria-labelledby="createChatGroupLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form method="POST" action="{{ route('chat-groups') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createChatGroupLabel">Create Chat Group</h5>

                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <label for="group-name">Group Name</label>
                        <input type="text" name="name" id="group-name" class="form-control" placeholder="Enter group name" required>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Create</button>&nbsp;
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>

                <script>
                    $(document).ready(function () {
                        $('#send-button').click(function () {
                            let message = $('#message-input').val();
                            let groupId = $('#group-id').val(); // Pass this from the Blade context

                            if (message.trim() === "") return;

                            $.ajax({
                                url: "{{ route('chat-group.send-message') }}",
                                method: "POST",
                                data: {
                                    _token: "{{ csrf_token() }}",
                                    message: message,
                                    group_id: groupId
                                },
                                success: function (response) {
                                    $('#message-input').val('');
                                    addMessage(response.sender_name, response.message, response.timestamp);
                                },
                                error: function () {
                                    alert("❌ Failed to send message.");
                                }
                            });
                        });
                    });
                    async function fetchInvitedUsers(groupId, tableBodyId) {
                        try {
                            const response = await fetch(`/chat-groups/${groupId}/invited-users`);
                            const data = await response.json();
                            updateInviteModal(data.users, data.invited_users, tableBodyId);
                        } catch (error) {
                            console.error("Error fetching invited users:", error);
                        }
                    }

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

                    document.addEventListener("DOMContentLoaded", function () {
                        document.getElementById("member-list").addEventListener("click", function (e) {
                            if (e.target && e.target.classList.contains("invite-user-btn")) {
                                const groupId = e.target.dataset.groupId;
                                const modalId = `inviteUserModal${groupId}`;
                                const tableBodyId = `staffTableBody${groupId}`;

                                const modalElement = document.getElementById(modalId);
                                const tableBody = document.getElementById(tableBodyId);

                                if (!modalElement || !tableBody) {
                                    console.error("❌ Modal or Table Body not found!");
                                    return;
                                }

                                fetchInvitedUsers(groupId, tableBodyId);

                                const modal = new bootstrap.Modal(modalElement);
                                modal.show();
                            }
                        });
                    });

                    function loadGroupMessages(groupId) {
                        fetch(`/chat-groups/${groupId}/messages`)
                            .then(response => response.json())
                            .then(messages => {
                                const chatBox = document.getElementById("chat-box");
                                chatBox.innerHTML = ""; // Clear existing messages

                                if (messages.length === 0) {
                                    chatBox.innerHTML = "<p class='text-muted'>No messages yet.</p>";
                                    return;
                                }

                                messages.forEach(msg => {
                                    addMessage(msg.user?.name, msg.message, msg.created_at);
                                });

                                // Optional: scroll to bottom
                                //chatBox.scrollTop = chatBox.scrollHeight;

                            })
                            .catch(err => {
                                console.error("Failed to fetch messages:", err);
                            });
                    }

                    function addMessage(sender, message, timestamp) {
                        const chatBox = document.getElementById("chat-box");

                        // Create the message container
                        const messageDiv = document.createElement("div");
                        messageDiv.classList.add("message", sender === "{{ auth()->user()->name }}" ? "sent" : "received");

                        // Create the inner message wrapper
                        const messageContainer = document.createElement("div");
                        messageContainer.classList.add("message-container");

                        // Create sender name element
                        const senderNameDiv = document.createElement("div");
                        senderNameDiv.textContent = sender;
                        senderNameDiv.style.fontSize = "12px";
                        senderNameDiv.style.fontWeight = "bold";
                        senderNameDiv.style.marginBottom = "4px";

                        // Create message bubble
                        const bubble = document.createElement("div");
                        bubble.classList.add("message-bubble");
                        bubble.textContent = message;

                        // Create timestamp element
                        const timestampDiv = document.createElement("div");
                        timestampDiv.classList.add("message-timestamp");

                        const dateObj = timestamp ? new Date(timestamp) : new Date();
                        const timeString = dateObj.toLocaleString([], {
                            hour: '2-digit',
                            minute: '2-digit',
                            hour12: true,
                            day: '2-digit',
                            month: 'short',
                            year: 'numeric'
                        });

                        timestampDiv.textContent = timeString;

                        // Append sender name, bubble & timestamp to message container
                        messageContainer.appendChild(senderNameDiv);
                        messageContainer.appendChild(bubble);
                        messageContainer.appendChild(timestampDiv);

                        // Append message container to messageDiv
                        messageDiv.appendChild(messageContainer);

                        // Append messageDiv to chatBox
                        chatBox.appendChild(messageDiv);

                        // Scroll to latest message
                        chatBox.scrollTop = chatBox.scrollHeight;
                    }

                    document.addEventListener("DOMContentLoaded", function () {
                        const groupCards = document.querySelectorAll(".group-card");

                        groupCards.forEach(card => {
                            card.addEventListener("click", function () {
                                const groupId = this.dataset.groupId;
                                loadGroupMessages(groupId);
                                $('#group-id').val(groupId);
                                fetch(`/chat-groups/${groupId}/members`)
                                    .then(response => response.json())
                                    .then(members => {
                                        const membersList = document.getElementById("member-list");
                                        membersList.innerHTML = ""; // clear previous members

                                        if (members.length === 0) {
                                            membersList.innerHTML = "<li>No members found.</li>";
                                        } else {
                                            let creator = "";
                                            members.forEach(member => {
                                                const groupId = this.dataset.groupName;
                                                document.getElementById("recipientLabel").textContent = this.dataset.groupName;
                                                const li = document.createElement("li");
                                                li.classList.add("user");
                                                li.style.display = "flex";
                                                li.style.justifyContent = "space-between";
                                                li.style.alignItems = "center";
                                                li.style.marginBottom = "8px";
                                                li.textContent = member.name; // adjust field as needed
                                                membersList.appendChild(li);
                                                if(member.is_creator===1){
                                                    creator = member.user_id;
                                                }
                                            });
                                            let userLoggin = "{{ auth()->user()->id }}";

                                            if (creator==userLoggin) {
                                                const li = document.createElement("li");
                                                const inviteButton = document.createElement("button");
                                                inviteButton.classList.add("btn", "btn-secondary", "invite-user-btn");
                                                inviteButton.setAttribute("data-toggle", "modal");
                                                inviteButton.setAttribute("data-target", "#inviteUserModal" + groupId); // Assuming $group->id is available
                                                inviteButton.setAttribute("data-group-id", groupId); // Assuming $group->id is available
                                                inviteButton.textContent = "Invite User";

                                                // Append the button to the li
                                                li.appendChild(inviteButton);
                                                membersList.appendChild(li);
                                            }
                                        }
                                    })
                                    .catch(error => {
                                        console.error("Failed to fetch members:", error);
                                    });
                            });
                        });
                    });
                </script>


@endsection
