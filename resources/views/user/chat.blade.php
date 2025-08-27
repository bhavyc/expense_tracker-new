<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Chat with Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        #chat-box { height: 400px; overflow-y: scroll; border: 1px solid #ccc; padding: 10px; }
        .message { margin-bottom: 10px; }
        .message.me { text-align: right; color: blue; }
        .message.admin { text-align: left; color: green; }
    </style>
</head>
<body>
<div class="container mt-4">
    <h3>Chat with Admin: {{ $admin->name }}</h3>
    <div id="chat-box"></div>

    <form id="chat-form" class="mt-3">
        <input type="hidden" id="receiver_id" value="{{ $admin->id }}">
        <div class="input-group">
            <input type="text" id="message" class="form-control" placeholder="Type message...">
            <button type="submit" class="btn btn-primary">Send</button>
        </div>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- <script>
let receiverId = "{{ $admin->id }}";
let myId = {{ Auth::id() }};
let csrfToken = $('meta[name="csrf-token"]').attr('content');

function loadMessages() {
    $.get("/account/messages/" + receiverId, function(data) {
        $("#chat-box").html('');
        data.forEach(msg => {
            let cls = msg.sender_id == myId ? 'me' : 'admin';
            let sender = msg.sender_id == myId ? 'Me' : 'Admin';
            $("#chat-box").append(`<div class="message ${cls}"><b>${sender}:</b> ${msg.message}</div>`);
        });
        $("#chat-box").scrollTop($("#chat-box")[0].scrollHeight);
    });
}

// Load messages every 1 second
loadMessages();
setInterval(loadMessages, 1000);

$("#chat-form").submit(function(e){
    e.preventDefault();
    let message = $("#message").val();
    if(message.trim() == '') return;

    $.post("/account/messages", {
        receiver_id: receiverId,
        message: message,
        _token: csrfToken
    }, function(){
        $("#message").val('');
        loadMessages();
    });
});
</script> -->

<script>
let receiverId = "{{ $admin->id }}"; // Admin ID
let myId = {{ Auth::id() }};          // Logged-in user ID
let csrfToken = $('meta[name="csrf-token"]').attr('content');

function loadMessages() {
    $.get("/account/messages/" + receiverId, function(data) {
        $("#chat-box").html('');
        data.forEach(msg => {
            let cls = msg.sender_id == myId ? 'me' : 'admin';
            let sender = msg.sender_id == myId ? 'Me' : 'Admin';
            $("#chat-box").append(`<div class="message ${cls}"><b>${sender}:</b> ${msg.message}</div>`);
        });
        $("#chat-box").scrollTop($("#chat-box")[0].scrollHeight);
    });
}

loadMessages();
setInterval(loadMessages, 1000); // हर 1 सेकंड refresh

$("#chat-form").submit(function(e){
    e.preventDefault();
    let message = $("#message").val();
    if(message.trim() == '') return;

    $.post("/account/messages", {
        receiver_id: receiverId,
        message: message,
        _token: csrfToken
    }, function(){
        $("#message").val('');
        loadMessages();
    });
});
</script>

</body>
</html>
