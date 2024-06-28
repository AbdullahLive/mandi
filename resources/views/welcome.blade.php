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
            bottom: 10px;
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
            border-radius: 30px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            font-family: Arial, sans-serif;
            display: none;
        }

        .chatbot-header {
            background-color: #4caf50;
            color: #fff;
            padding: 5px 5px 5px 15px;
            position: relative;
            display: flex;
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
            padding: 15px;
            background-color: #4caf50;
            margin-bottom: 7px;
        }

        #rag-chatbot-input {
            flex: 1;
            padding: 8px;
            border: none;
            border-radius: 5px;
            margin-right: 10px;
        }

        #rag-chatbot-input:focus-visible {
            outline: none;
        }

        #rag-chatbot-submit {
            border: none;
            background-color: #4caf50;
            color: #fff;
            border-radius: 20px;
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
            background-color: #e9f5e8;
            color: #000;
            align-self: flex-end;
            text-align: right;
            font-size: 14px;
            padding: 10px;
            border-radius: 15px
        }

        .chatbot-message.bot {
            color: #fff;
            align-self: flex-start;
            text-align: left;
            font-size: 14px;
            position: relative;
            padding: 10px;
            border-radius: 15px;
            display: flex;
            align-items: flex-end;
        }

        .chatbot-message.bot:before {
            content: '';
            position: absolute;
            top: 15px;
            left: -10px;
            border-width: 10px;
            border-style: solid;
        }

        .loading {
            background-color: white;
            color: #4caf50;
            align-self: flex-start;
            text-align: left;
            font-style: italic;
            padding: 10px;
            border-radius: 15px;
        }

        .chat-icon {
            width: 40px;
            height: 40px;
            background-image: url('logo.png');
            background-repeat: no-repeat;
            background-size: contain;
            margin-right: 10px;
            align-self: flex-end;
            position: absolute;
            bottom: 10px;
            left: 10px;
        }

        .chatbot-message.bot .chat-content {
            display: flex;
            align-items: flex-start;
        }

        .chatbot-message.bot .chat-bubble {
            background-color: #4CAF50;
            color: white;
            padding: 15px;
            border-radius: 15px;
            position: relative;
            font-family: Arial, sans-serif;
            font-size: 14px;
            line-height: 1.4;
            display: flex;
            align-items: flex-end;
            margin-left: 60px; /* Ensure there is space for the icon */
        }
    </style>
</head>

<body class="antialiased">
    <button id="chatbot-button"><img src="{{asset('logo-1.png')}}" style="object-fit: contain;"></button>

    <div id="rag-chatbot-container">
        <div class="chatbot-header">
            <img src="{{asset('logo-1.png')}}" style="object-fit: contain;">
            <h3 style="font-size: 15px;">CHAT WITH OUR VIRTUAL ASSISTANT</h3>
            <button class="close-button" id="close-chatbot">&times;</button>
        </div>
        <div class="chatbot-body">
            <div id="rag-chatbot-messages" class="chatbot-messages">
                <!-- Chat messages will be dynamically added here -->
            </div>
            <div class="chatbot-input">
                <input type="text" id="rag-chatbot-input" name="question" placeholder="Ask me anything...">
                <button id="rag-chatbot-submit"><img src="{{asset('send.png')}}"></button>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        document.getElementById('chatbot-button').addEventListener('click', function () {
            var chatbotContainer = document.getElementById('rag-chatbot-container');
            chatbotContainer.style.display = chatbotContainer.style.display === 'none' ? 'block' : 'none';
        });

        document.getElementById('close-chatbot').addEventListener('click', function () {
            var chatbotContainer = document.getElementById('rag-chatbot-container');
            chatbotContainer.style.display = 'none';
            resetChat();
        });

        document.getElementById('rag-chatbot-submit').addEventListener('click', function () {
            submitMessage();
        });

        document.getElementById('rag-chatbot-input').addEventListener('keydown', function (event) {
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
                    success: function (response) {
                        messageContainer.removeChild(loadingDiv);
                        var botMessageDiv = document.createElement('div');
                        botMessageDiv.className = 'chatbot-message bot';

                        var chatIconDiv = document.createElement('div');
                        chatIconDiv.className = 'chat-icon';

                        var chatBubbleDiv = document.createElement('div');
                        chatBubbleDiv.className = 'chat-bubble';
                        chatBubbleDiv.textContent = response.answer;

                        botMessageDiv.appendChild(chatIconDiv);
                        botMessageDiv.appendChild(chatBubbleDiv);

                        messageContainer.appendChild(botMessageDiv);

                        // Scroll to the bottom of the messages
                        messageContainer.scrollTop = messageContainer.scrollHeight;
                    },
                    error: function (xhr, status, error) {
                        messageContainer.removeChild(loadingDiv);
                        var botMessageDiv = document.createElement('div');
                        botMessageDiv.className = 'chatbot-message bot';

                        var chatIconDiv = document.createElement('div');
                        chatIconDiv.className = 'chat-icon';

                        var chatBubbleDiv = document.createElement('div');
                        chatBubbleDiv.className = 'chat-bubble';
                        chatBubbleDiv.textContent = 'An error occurred: ' + xhr.responseText;

                        botMessageDiv.appendChild(chatIconDiv);
                        botMessageDiv.appendChild(chatBubbleDiv);

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
