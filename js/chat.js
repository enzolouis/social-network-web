function loadChat(login, otherLogin) {
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
    input.value = message.getElementsByClassName("msg-text")[0].innerHTML;
}

function copyMessage(messageId) {
    let message = document.getElementById(messageId);
    let messageContent = message.getElementsByClassName("msg-text")[0].innerHTML;
    navigator.clipboard.writeText(messageContent);
}