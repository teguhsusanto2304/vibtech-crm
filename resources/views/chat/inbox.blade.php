@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
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
                                <strong>Online Users</strong>
                            </div>
                            <div class="card-body">
                                <ul id="online-users-list" style="list-style: none; padding: 0;"></ul>
                            </div>
                        </div>
                        <div class="card mt-4">
                            <div class="card-header text-bg-danger">
                                <strong>Offline Users</strong>
                            </div>
                            <div class="card-body">
                                <ul id="offline-users-list" style="list-style: none; padding: 0;"></ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-9" >
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
                                <input type="text" id="message" placeholder="Type a message">
                                <button onclick="sendMessage()">Send</button>
                            </div>
                        </div>
                    </div>
                </div>
                </div>

                    <script>
                        const knownSenders = new Set();
                        document.addEventListener("DOMContentLoaded", function () {
                            const messageInput = document.getElementById("message");

                            messageInput.addEventListener("keydown", function (event) {
                                if (event.key === "Enter" && !event.shiftKey) {
                                    event.preventDefault(); // Prevent newline or form submission
                                    sendMessage(); // Call your sendMessage function
                                }
                            });
                        });

                        let userId = "{{ auth()->user()->name }}";
                        let recipientId = "";
                        let socket;

                        // Function to fetch previous messages
                        function loadPreviousMessages() {
                            recipientId = document.getElementById("recipient").value;
                            if (!recipientId) {
                                alert("Enter a recipient ID.");
                                return;
                            }

                            fetch(` {{ env('CHAT_URL') }}/messages/${userId}/${recipientId}`)
                                .then(response => response.json())
                                .then(messages => {
                                    const chatBox = document.getElementById("chat-box");
                                    chatBox.innerHTML = ""; // Clear chat box before loading previous messages

                                    messages.forEach(msg => {
                                        addMessage(msg.sender_id, msg.message, msg.timestamp);
                                    });

                                    startWebSocket();
                                })
                                .catch(error => console.error("Error loading messages:", error));
                        }

                        // Function to start WebSocket connection
                        function startWebSocket() {
                            socket = new WebSocket(`{{ env('WEBSOCKET_CHAT_URL') }}/ws1/${userId}`);

                            socket.onopen = function () {
                                console.log("Connected to WebSocket server");
                                updateChatBadge();
                            };

                            socket.onmessage = function (event) {
                                const messageData = JSON.parse(event.data);

                                if (messageData.type === "chat") {
                                    const sender = messageData.sender;
                                    const isFirstMessageFromSender = !knownSenders.has(sender);
                                    if (isFirstMessageFromSender) {
                                        console.log("First time receiving message from:", sender);
                                        knownSenders.add(sender);
                                        document.getElementById("recipient").value = sender;
                                        loadPreviousMessages();
                                    }

                                    const dateObj = new Date();
                                    const timeString = dateObj.toLocaleString([], {
                                        hour: '2-digit',
                                        minute: '2-digit',
                                        hour12: true,
                                        day: '2-digit',
                                        month: 'short',
                                        year: 'numeric'
                                    }); // "10:30 PM"

                                    addMessage(messageData.sender, messageData.message);
                                } else if (messageData.type === "online_users") {
                                    updateOnlineUsersList(messageData.users);
                                    updateOfflineUsersList(messageData.user_offline);
                                }
                            };

                            socket.onclose = function () {
                                console.log("WebSocket connection closed");
                            };
                        }

                        // Function to update online users list
                        function updateOnlineUsersList(users) {
                            const userList = document.getElementById("online-users-list");
                            userList.innerHTML = "";
                            let userLogin = "{{ auth()->user()->name }}";

                            users.forEach(user => {
                                if (user.username !== userLogin) {
                                    const li = document.createElement("li");
                                    li.classList.add("user");
                                    li.style.display = "flex";
                                    li.style.justifyContent = "space-between";
                                    li.style.alignItems = "center";
                                    li.style.marginBottom = "8px";

                                    // Username span
                                    const nameSpan = document.createElement("span");
                                    nameSpan.textContent = user.username;

                                    // Badge span
                                    const badge = document.createElement("span");
                                    badge.classList.add("badge");
                                    badge.textContent = user.unread > 0 ? user.unread : "";
                                    badge.style.background = "red";
                                    badge.style.color = "white";
                                    badge.style.borderRadius = "50%";
                                    badge.style.padding = "4px 8px";
                                    badge.style.fontSize = "0.8rem";
                                    badge.style.marginLeft = "10px";
                                    badge.style.display = user.unread > 0 ? "inline-block" : "none";

                                    li.appendChild(nameSpan);
                                    li.appendChild(badge);

                                    li.onclick = () => {
                                        document.getElementById("recipient").value = user.username;
                                        recipientId = user.username;
                                        loadPreviousMessages();

                                        let recipientLabel = document.getElementById("recipientLabel");
                                        recipientLabel.textContent = user.username;

                                        // Clear unread badge
                                        badge.textContent = "";
                                        badge.style.display = "none";
                                    };

                                    userList.appendChild(li);
                                }
                            });

                        }

                        function updateOfflineUsersList(users) {
                            const userList = document.getElementById("offline-users-list");
                            userList.innerHTML = "";
                            let userLogin = "{{ auth()->user()->name }}";

                            users.forEach(user => {
                                if (user.username !== userLogin) {
                                    const li = document.createElement("li");
                                    li.classList.add("user");
                                    li.style.display = "flex";
                                    li.style.justifyContent = "space-between";
                                    li.style.alignItems = "center";
                                    li.style.marginBottom = "8px";

                                    // Username span
                                    const nameSpan = document.createElement("span");
                                    nameSpan.textContent = user.username;

                                    // Badge span
                                    const badge = document.createElement("span");
                                    badge.classList.add("badge");
                                    badge.textContent = user.unread > 0 ? user.unread : "";
                                    badge.style.background = "red";
                                    badge.style.color = "white";
                                    badge.style.borderRadius = "50%";
                                    badge.style.padding = "4px 8px";
                                    badge.style.fontSize = "0.8rem";
                                    badge.style.marginLeft = "10px";
                                    badge.style.display = user.unread > 0 ? "inline-block" : "none";

                                    li.appendChild(nameSpan);
                                    li.appendChild(badge);

                                    li.onclick = () => {
                                        document.getElementById("recipient").value = user.username;
                                        recipientId = user.username;
                                        loadPreviousMessages();

                                        let recipientLabel = document.getElementById("recipientLabel");
                                        recipientLabel.textContent = user.username;

                                        // Clear unread badge
                                        badge.textContent = "";
                                        badge.style.display = "none";
                                    };

                                    userList.appendChild(li);
                                }
                            });
                        }

                        // Function to add a message bubble
                        function addMessage(sender, message, timestamp) {
                            const chatBox = document.getElementById("chat-box");

                            // Create the message container
                            const messageDiv = document.createElement("div");
                            messageDiv.classList.add("message", sender === userId ? "sent" : "received");

                            // Create the inner message wrapper
                            const messageContainer = document.createElement("div");
                            messageContainer.classList.add("message-container");

                            // Create message bubble
                            const bubble = document.createElement("div");
                            bubble.classList.add("message-bubble");
                            bubble.textContent = message;

                            // Create timestamp element
                            const timestampDiv = document.createElement("div");
                            timestampDiv.classList.add("message-timestamp");

                            // Convert and format timestamp
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

                            // Append bubble & timestamp to message container
                            messageContainer.appendChild(bubble);
                            messageContainer.appendChild(timestampDiv);

                            // Append message container to messageDiv
                            messageDiv.appendChild(messageContainer);

                            // Append final messageDiv to chatBox
                            chatBox.appendChild(messageDiv);

                            // Scroll to latest message
                            chatBox.scrollTop = chatBox.scrollHeight;
                        }



                        // Function to send a new message
                        function sendMessage() {
                            const message = document.getElementById("message").value;

                            if (!recipientId || !message) {
                                alert("Please enter a recipient ID and a message.");
                                return;
                            }

                            const data = JSON.stringify({ type: "chat", to: recipientId, message: message });
                            socket.send(data);
                            addMessage(userId, message);
                            document.getElementById("message").value = "";
                        }

                        // Start WebSocket when the page loads
                        startWebSocket();
                    </script>
@endsection
