@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
    <!-- custom-icon Breadcrumb-->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-custom-icon">
            @foreach ($breadcrumb as $item )
            <li class="breadcrumb-item">
                <a href="javascript:void(0);">{{ $item }}</a>
                <i class="breadcrumb-icon icon-base bx bx-chevron-right align-middle"></i>
            </li>
            @endforeach
        </ol>
    </nav>

    <h3>{{ $title }}</h3>
    <!-- Sneat Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons/css/boxicons.min.css">

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    <style>
        body {
            background-color: #f4f5fa;
        }
        .chat-container {
            height: 100vh;
        }
        .chat-sidebar {
            background: #ffffff;
            border-right: 1px solid #ddd;
            overflow-y: auto;
        }
        .chat-box {
            background: #fff;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .chat-messages {
            flex-grow: 1;
            overflow-y: auto;
            padding: 10px;
        }
        .chat-message {
            margin-bottom: 10px;
        }
        .chat-message.sent {
            text-align: right;
        }
        .chat-message .message-content {
            display: inline-block;
            padding: 10px 14px;
            border-radius: 10px;
        }
        .chat-message.sent .message-content {
            background-color: #696cff;
            color: white;
        }
        .chat-message.received .message-content {
            background-color: #f1f2f6;
        }
    </style>
    <div class="container-fluid chat-container d-flex">
        <!-- Sidebar User List -->
        <div class="col-md-3 chat-sidebar p-3">
            <h5 class="mb-3">Users</h5>
            <ul class="list-group" id="userList">

                <!-- Marketing Department -->
                <li class="list-group-item bg-primary text-white fw-bold">Marketing Department</li>
                <li class="list-group-item user d-flex align-items-center" data-user="Houston">
                    <img src="../assets/img/avatars/1.png" alt class="w-px-30 h-auto rounded-circle" style="margin-right: 10px;"/>Houston
                </li>
                <li class="list-group-item user d-flex align-items-center" data-user="David Liem">

                    <img src="../assets/img/avatars/3.png" alt class="w-px-30 h-auto rounded-circle" style="margin-right: 10px;"/> David Liem
                </li>
                <!-- IT Department -->
                <li class="list-group-item bg-primary text-white fw-bold">IT Department</li>
                <li class="list-group-item user d-flex align-items-center" data-user="Trisshan">

                    <img src="../assets/img/avatars/7.png" alt class="w-px-30 h-auto rounded-circle" style="margin-right: 10px;"/> Trrishan
                </li>
                <li class="list-group-item user d-flex align-items-center" data-user="Teguh">

                    <img src="../assets/img/avatars/5.png" alt class="w-px-30 h-auto rounded-circle" style="margin-right: 10px;"/> Teguh
                </li>

                <!-- HR Department -->
                <li class="list-group-item bg-success text-white fw-bold">HR Department</li>
                <li class="list-group-item user d-flex align-items-center" data-user="Ruby">

                    <img src="../assets/img/avatars/2.png" alt class="w-px-30 h-auto rounded-circle" style="margin-right: 10px;"/> Ruby
                </li>
            </ul>

        </div>

        <!-- Chat Box -->
        <div class="col-md-9 d-flex flex-column chat-box">
            <div class="chat-header p-3 border-bottom bg-white">
                <h5 class="mb-0">Chat with <span id="currentUser">Houston</span></h5>
            </div>
            <div class="chat-messages" id="chatBox">
                <!-- Messages appear here -->
            </div>
            <div class="p-3 border-top bg-light d-flex">
                <input type="text" id="messageInput" class="form-control me-2" placeholder="Type a message..." style="background-color: #fff">
                <button class="btn btn-primary" id="sendMessage" ><i class="bx bx-send bx-md"></i></button>

            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            let currentUser = "Houston";

            // Change user when clicked
            $(".user").click(function () {
                currentUser = $(this).data("user");
                $("#currentUser").text(currentUser);
                $("#chatBox").html(""); // Clear chat
            });

            // Send message
            $("#sendMessage").click(function () {
                let message = $("#messageInput").val();
                if (message.trim() !== "") {
                    let msgHtml = '<div class="chat-message sent"><div class="message-content">' + message + '</div></div>';
                    $("#chatBox").append(msgHtml);
                    $("#messageInput").val(""); // Clear input
                    $(".chat-messages").scrollTop($(".chat-messages")[0].scrollHeight);
                }
            });

            // Simulate receiving a message
            setTimeout(function () {
                let receivedMsg = '<div class="chat-message received"><div class="message-content">Hello! How are you?</div></div>';
                $("#chatBox").append(receivedMsg);
            }, 3000);
        });
    </script>

</div>
@endsection
