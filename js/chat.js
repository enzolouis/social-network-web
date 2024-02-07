var connectedUser;
var otherUser;

function loadChat(login, otherLogin) {
    connectedUser = login;
    otherUser = otherUser;
    let chat = document.getElementById("chat-box");
    while (chat.childElementCount > 1) {
        chat.removeChild(chat.lastChild);
    }
    $.ajax({
        type: 'POST',
        url: '../functions/showMessages.php',
        data: {
            user: login,
            otherUser: otherLogin,
        },
        beforeSend: function() {
            $('#chat-box.loading-gif').show();
        },
        success: function(data) {
            $('#chat-box').append(data);
            $('#chat-box.loading-gif').hide();
        }
    })
}

function loadHeader(otherLogin) {
    let chatHeader = document.getElementById("chat-header");
    while (chatHeader.childElementCount > 1) {
        chatHeader.removeChild(chatHeader.lastChild);
    }
    $.ajax({
        type: 'POST',
        url: '../functions/showHeader.php',
        data: {
            otherUser: otherLogin,
        },
        beforeSend: function() {
            $('#chat-header.loading-gif').show();
        },
        success: function(data) {
            $('#chat-header').append(data);
            $('#chat-header.loading-gif').hide();
        }
    })
}

function editMessage(messageId) {
    let message = document.getElementById(messageId);
    let input = document.getElementById("chat-message-text");

    input.setAttribute("id-message", messageId);
    input.value = message.getElementsByClassName("msg-text")[0].innerHTML;
}

function copyMessage(messageId) {
    let message = document.getElementById(messageId);
    let messageContent = message.getElementsByClassName("msg-text")[0].innerHTML;
    navigator.clipboard.writeText(messageContent);
}

function sendMessage() {
    let input = document.getElementById("chat-message-text");
    let chatContent = document.getElementById("chat-box").innerHTML;
    let messageId = input.getAttribute("id-message");
    let msg = input.value;

    if(messageId == null || messageId == "") { 

    } else { // If it's an update
        $.ajax({
            type: 'POST',
            url: '../functions/updateMessageText.php',
            data: {
                id: messageId,
                text: msg,
                user: connectedUser,
                otherUser: otherUser,
                chatContent: chatContent,
            },
            success: function(data) {
                if(data) {
                    console.log("%c SUCCES: Update message", "color:green;");

                    input.value = "";
                    input.removeAttribute("id-message");
                    document.getElementById(messageId).getElementsByClassName("msg-text")[0].innerHTML = msg;

                    chat = document.getElementById("chat-box").innerHTML;
                    overrideChatCache(chat);
                } else {
                    console.log("%c ERREUR: Update message", "color:red;");
                }
            }
        })
    }
}

function overrideChatCache(chatContent) {
    $.ajax({
        type: 'POST',
        url: '../functions/cache.php',
        data: {
            function: "chat",

            user: connectedUser,
            other: otherUser,
            chat: chatContent
        },
        success: function(data) {
            if(data) console.log("%c SUCCES: Cache chat override", "color:green;");
            else     console.log("%c ERREUR: Cache chat override", "color:red;");
        }
    })
}