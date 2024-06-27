<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    
   
    <title>Chatbot</title>
    <style>
        body {
            font-family: 'Nunito', sans-serif;
        }

        #chatbot-button {
            position: fixed;
            bottom: 20px;
            right: 20px;
            padding: 15px 20px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        #rag-chatbot-container {
            position: fixed;
            bottom: 80px;
            right: 20px;
            width: 100%;
            max-width: 400px;
            height: 70%;
            border: 1px solid #ccc;
            border-radius: 5px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            font-family: Arial, sans-serif;
            display: none;
        }

        .chatbot-header {
            background-color: #28a745; /* Green color */
            color: #fff;
            padding: 5px;
            text-align: center;
            position: relative;
        }

        .close-button {
            position: absolute;
            right: 10px;
            top: 10px;
            background-color: transparent;
            border: none;
            color: #fff;
            font-size: 20px;
            cursor: pointer;
        }

        .chatbot-body {
            height: calc(100% - 50px);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .chatbot-messages {
            flex: 1;
            overflow-y: auto;
            padding: 10px;
            display: flex;
            flex-direction: column;
            background-color: white;
        }

        .chatbot-input {
            display: flex;
            align-items: center;
            padding: 10px;
            background-color: #f8f9fa;
            margin-bottom: 20px;
        }

        #rag-chatbot-input {
            flex: 1;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 20px; /* Rounded shape */
            margin-right: 10px;
        }

        #rag-chatbot-submit {
            padding: 8px 16px;
            border: none;
            background-color: #28a745; /* Green color */
            color: #fff;
            border-radius: 20px; /* Rounded shape */
            cursor: pointer;
        }

        .chatbot-message {
            max-width: 80%;
            padding: 8px;
            border-radius: 4px;
            margin-bottom: 5px;
            display: inline-block;
        }

        .chatbot-message.user {
            background-color: #28a745;
            color: #fff;
            align-self: flex-end;
            text-align: right;
        }

        .chatbot-message.bot {
            background-color: #f0f0f0;
            color: #000;
            align-self: flex-start;
            text-align: left;
        }

        .loading {
            background-color: #f0f0f0;
            color: #000;
            align-self: flex-start;
            text-align: left;
            font-style: italic;
        }
    </style>
</head>
<body class="antialiased">
    <button id="chatbot-button"><i class="fas fa-comments"></i> AI Chat Bot</button>

    <div id="rag-chatbot-container">
        <div class="chatbot-header">
            <h3> AI Chat Bot</h3>
            <button class="close-button" id="close-chatbot">&times;</button>
        </div>
        <div class="chatbot-body">
            <div id="rag-chatbot-messages" class="chatbot-messages">
                <!-- Chat messages will be dynamically added here -->
            </div>
            <div class="chatbot-input">
                <input type="text" id="rag-chatbot-input" name="question" placeholder="Ask me anything...">
                <button id="rag-chatbot-submit">Ask</button>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
      document.getElementById('chatbot-button').addEventListener('click', function() {
    var chatbotContainer = document.getElementById('rag-chatbot-container');
    chatbotContainer.style.display = chatbotContainer.style.display === 'none' ? 'block' : 'none';
});

document.getElementById('close-chatbot').addEventListener('click', function() {
    var chatbotContainer = document.getElementById('rag-chatbot-container');
    chatbotContainer.style.display = 'none';
    resetChat();
});

document.getElementById('rag-chatbot-submit').addEventListener('click', function() {
    submitMessage();
});

document.getElementById('rag-chatbot-input').addEventListener('keydown', function(event) {
    if (event.keyCode === 13) {
        submitMessage();
    }
});

function submitMessage() {
    var inputField = document.getElementById('rag-chatbot-input');
    var userMessage = inputField.value;
    if (userMessage) {
        var messageContainer = document.getElementById('rag-chatbot-messages');

        var userMessageDiv = document.createElement('div');
        userMessageDiv.className = 'chatbot-message user';
        userMessageDiv.textContent = userMessage;
        messageContainer.appendChild(userMessageDiv);

        inputField.value = '';

        // Scroll to the bottom of the messages
        messageContainer.scrollTop = messageContainer.scrollHeight;

        // Display loading message
        var loadingDiv = document.createElement('div');
        loadingDiv.className = 'chatbot-message bot loading';
        loadingDiv.textContent = '...';
        messageContainer.appendChild(loadingDiv);

        // Handle the chatbot response
        $.ajax({
            url: "{{ route('chatbot.ask') }}",
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                question: userMessage
            },
            success: function(response) {
                messageContainer.removeChild(loadingDiv);
                var botMessageDiv = document.createElement('div');
                botMessageDiv.className = 'chatbot-message bot';
                botMessageDiv.textContent = response.answer;
                messageContainer.appendChild(botMessageDiv);

                // Scroll to the bottom of the messages
                messageContainer.scrollTop = messageContainer.scrollHeight;
            },
            error: function(xhr, status, error) {
                messageContainer.removeChild(loadingDiv);
                var botMessageDiv = document.createElement('div');
                botMessageDiv.className = 'chatbot-message bot';
                botMessageDiv.textContent = 'An error occurred: ' + xhr.responseText;
                messageContainer.appendChild(botMessageDiv);

                // Scroll to the bottom of the messages
                messageContainer.scrollTop = messageContainer.scrollHeight;
            }
        });
    }
}

function resetChat() {
    var messageContainer = document.getElementById('rag-chatbot-messages');
    messageContainer.innerHTML = '';
}

    </script>
</body>
</html>
