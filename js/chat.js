var connectedUser;
var otherUser;

function loadHeaderAndChat(login, otherLogin) {
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
    connectedUser = login;
    otherUser = otherUser;
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