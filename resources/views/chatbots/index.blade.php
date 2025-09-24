<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ollama Chatbot</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f0f2f5; }
        .chat-container {
            max-width: 800px;
            margin: auto;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .chat-box {
            height: 60vh;
            overflow-y: scroll;
            padding: 20px;
            background-color: #fff;
        }
        .message {
            margin-bottom: 15px;
        }
        .message.user {
            text-align: right;
        }
        .message.bot {
            text-align: left;
        }
        .message .bubble {
            display: inline-block;
            padding: 10px 15px;
            border-radius: 20px;
            max-width: 70%;
        }
        .message.user .bubble {
            background-color: #0d6efd;
            color: white;
        }
        .message.bot .bubble {
            background-color: #e9ecef;
            color: #212529;
        }
        .input-group {
            padding: 20px;
            background-color: #fff;
            border-top: 1px solid #dee2e6;
        }
    </style>
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="chat-container">
            <div class="chat-box" id="chat-box">
                </div>
            <div class="input-group">
                <input type="text" id="user-input" class="form-control" placeholder="Type a message...">
                <button class="btn btn-primary" type="button" id="send-btn">Send</button>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            const chatBox = $('#chat-box');
            const userInput = $('#user-input');
            const sendBtn = $('#send-btn');

            function appendMessage(sender, message) {
                const messageDiv = `<div class="message ${sender}"><div class="bubble">${message}</div></div>`;
                chatBox.append(messageDiv);
                chatBox.scrollTop(chatBox[0].scrollHeight);
            }

            function sendMessage() {
                const message = userInput.val().trim();
                if (message === '') return;

                appendMessage('user', message);
                userInput.val('');

                // API call to your Ollama endpoint
                $.ajax({
                    url: 'http://localhost:11434/api/generate',
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({
                        model: 'llama3',
                        prompt: message,
                        stream: false
                    }),
                    success: function(response) {
                        if (response.response) {
                            appendMessage('bot', response.response);
                        } else {
                            appendMessage('bot', 'Sorry, something went wrong.');
                        }
                    },
                    error: function(xhr, status, error) {
                        appendMessage('bot', 'Error connecting to the chatbot service.');
                        console.error("API Error:", error);
                    }
                });
            }

            sendBtn.on('click', sendMessage);

            userInput.on('keypress', function(e) {
                if (e.which === 13) { // Enter key
                    sendMessage();
                }
            });
        });
    </script>
</body>
</html>