function loadChat(login, otherLogin) {
    let chat = document.getElementById("chat-box");
    while (chat.childElementCount > 1) {
        chat.removeChild(chat.lastChild);
    }
    $.ajax({
        type: 'POST',
        url: 'showMessages.php',
        data: {
            user: login,
            otherUser: otherLogin,
        },
        beforeSend: function() {
            $('#loading-gif').show();
        },
        success: function(data) {
            $('#chat-box').append(data);
            $('#loading-gif').hide();
        }
    })
}