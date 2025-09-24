<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Ollama Chatbot</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <style>
        html, body {
            height: 100%;
            background-color: #343541; /* Dark background */
            color: #d1d5db;
        }
        .chat-container {
            display: flex;
            flex-direction: column;
            height: 100vh;
        }
        .chat-history {
            flex-grow: 1;
            overflow-y: auto;
            padding: 2rem 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 1.5rem; /* Spacing between messages */
        }
        .message-bubble {
            max-width: 80%;
            padding: 1rem;
            border-radius: 0.5rem;
        }
        .user-message {
            background-color: #444654;
            color: #ececf1;
            align-self: flex-end; /* Align to the right */
            margin-left: auto; /* Push to the right */
        }
        .bot-message {
            background-color: #343541;
            border: 1px solid #444654;
            color: #d1d5db;
            align-self: flex-start; /* Align to the left */
            margin-right: auto; /* Push to the left */
        }
        .input-area {
            background-color: #444654;
            padding: 1rem;
            border-top: 1px solid #555;
        }
        .input-group {
            max-width: 900px;
            margin: auto;
        }
        .input-group .form-control {
            background-color: #555;
            border: none;
            color: #d1d5db;
        }
        /* Markdown-specific styling for bot messages */
        .bot-message pre {
            background-color: #282a36;
            color: #f8f8f2;
            padding: 1rem;
            border-radius: 0.5rem;
            overflow-x: auto;
            word-wrap: break-word;
            white-space: pre-wrap;
        }
        .bot-message code {
            background-color: #555;
            padding: 2px 4px;
            border-radius: 4px;
            font-family: monospace;
        }
        .bot-message h1, .bot-message h2, .bot-message h3, .bot-message p, .bot-message ul, .bot-message ol {
            margin-top: 0;
            margin-bottom: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="chat-container">
        <div class="chat-history" id="response">
            <div class="initial-message text-center p-4">
                <h1 class="text-white mb-4">Vibtech ChatGPT</h1>
                <p>Start a conversation with vibtech LLM.</p>
            </div>
        </div>

        <div class="input-area">
            <div class="input-group">
                <input type="text" id="prompt" class="form-control form-control-lg rounded-pill" placeholder="Send a message..." required>
                <button class="btn btn-primary rounded-pill ms-2" type="button" id="send-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-send">
                        <line x1="22" y1="2" x2="11" y2="13"></line>
                        <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            const promptInput = $('#prompt');
            const sendBtn = $('#send-btn');
            const responseBox = $('#response');

            // Function to append a new message to the chat history
            function appendMessage(sender, text) {
                let messageContent;
                if (sender === 'bot') {
                    // Use marked.js to convert Markdown to HTML
                    messageContent = marked.parse(text);
                } else {
                    // For user messages, just use plain text
                    messageContent = text;
                }

                const messageHtml = `<div class="message-bubble ${sender}-message">${messageContent}</div>`;
                
                // Remove the initial welcome text when the first message is sent
                responseBox.find('.initial-message').remove();
                
                responseBox.append(messageHtml);
                responseBox.scrollTop(responseBox[0].scrollHeight);
            }

            // Handle form submission and API call
            async function sendMessage() {
                const prompt = promptInput.val().trim();
                if (prompt === '') return;

                appendMessage('user', prompt);
                promptInput.val('');
                
                // Add a "Thinking..." message from the bot
                appendMessage('bot', '...');

                try {
                    const res = await fetch("{{ route('v1.chatbot') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ prompt: prompt })
                    });
                    
                    const data = await res.json();
                    
                    // Remove the "Thinking..." message
                    $('.bot-message:last').remove();

                    if (data.message) {
                        appendMessage('bot', data.message.content);
                    } else if (data.error) {
                        appendMessage('bot', "Error: " + data.error);
                    } else {
                        appendMessage('bot', "Error: Unexpected response format.");
                    }
                } catch (err) {
                    $('.bot-message:last').remove();
                    appendMessage('bot', "Failed to reach Laravel backend.");
                }
            }

            sendBtn.on('click', sendMessage);
            promptInput.on('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    sendMessage();
                }
            });
        });
    </script>
</body>
</html>