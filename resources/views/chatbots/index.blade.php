<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Ollama Chat</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <h2>Chat with Ollama</h2>

    <form id="chat-form">
        <input type="text" id="prompt" placeholder="Ask something..." required>
        <button type="submit">Send</button>
    </form>

    <div id="response"></div>

    <script>
        document.getElementById('chat-form').addEventListener('submit', async function(e) {
            e.preventDefault();

            const prompt = document.getElementById('prompt').value;
            const responseBox = document.getElementById('response');

            responseBox.innerHTML = "Thinking...";

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

                if (data.message) {
                    responseBox.innerHTML = data.message.content;
                } else if (data.error) {
                    responseBox.innerHTML = "Error: " + data.error;
                } else {
                    responseBox.innerHTML = JSON.stringify(data);
                }

            } catch (err) {
                responseBox.innerHTML = "Failed to reach Laravel backend.";
            }
        });
    </script>
</body>
</html>
