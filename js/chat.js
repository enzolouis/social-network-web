var connectedUser;
var otherUser;

function loadHeaderAndChat(login, otherLogin) {
    connectedUser = login;
    otherUser = otherUser;
    let contactedUser = document.getElementById(otherLogin);
    let contactedUserOnClick = contactedUser.onclick;
    contactedUser.onclick = null;
    loadHeader(otherLogin);
    loadChat(login, otherLogin);
    $(document).ajaxStop(function(){
        contactedUser.onclick = contactedUserOnClick;
    });
}

function loadChat(login, otherLogin) {
    document.getElementById("chat-box").innerHTML = "";
    $.ajax({
        type: 'POST',
        url: '../functions/showMessages.php',
        data: {
            cacheOnly: "true",
            user: login,
            otherUser: otherLogin,
        },
        success: function(data) {
            $('#chat-box').append(data);
            $.ajax({
                type: 'POST',
                url: '../functions/showMessages.php',
                data: {
                    cacheOnly: "false",
                    user: login,
                    otherUser: otherLogin,
                },
                success: function(data) {
                    if (data != "No changes") {
                        $('#chat-box').html(data);
                    }
                }
            })
        }
    })
}

function loadHeader(otherLogin) {
    document.getElementById("chat-header").innerHTML = "";
    $.ajax({
        type: 'POST',
        url: '../functions/showHeader.php',
        data: {
            otherUser: otherLogin,
        },
        success: function(data) {
            $('#chat-header').append(data);
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

function deleteMessage(messageId) {
    let chat = document.getElementById("chat-box");
    let chatContent = chat.innerHTML;
    chat.removeChild(document.getElementById(messageId));
    $.ajax({
        type: 'POST',
        url: '../functions/deleteMessage.php',
        data: {
            id: messageId,
        },
        success: function(data) {
            alert("Message supprimé !");
            $.ajax({
                type: 'POST',
                url: '../functions/sessionCache.php',
                data: {
                    user: connectedUser,
                    otherUser: otherUser,
                    content: chatContent,
                }
            })
        }
    })
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