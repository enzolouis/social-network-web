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


/////  EDIT MESSAGE  /////////////////////////////
//  Gets the selected message's text and puts it inside the input field
//  Plus adds a 'id-message' attribute to the input, used to know later
//  if we're in the middle of an edit or a normal message
//
//  - Call     : onclick edit button (hover message)
//  - Arguments: messageId - self explanatory
//////////////////////////////////////////////////
function editMessage(messageId) {

    // Gets the message and the input field
    let message = document.getElementById(messageId);
    let input = document.getElementById("chat-message-text");

    // Sets the input 'id-message' attribute to the selected message's id
    input.setAttribute("id-message", messageId);

    // Puts the selected message inside the input field
    input.value = message.getElementsByClassName("msg-text")[0].innerHTML;
}


function copyMessage(messageId) {
    let message = document.getElementById(messageId);
    let messageContent = message.getElementsByClassName("msg-text")[0].innerHTML;
    navigator.clipboard.writeText(messageContent);
}


/////  SEND MESSAGE  /////////////////////////////
//  This function is the main one
//  It checks if the message we send is an edit or a brand new one
//  Depending on the result, it does the correct algorithm
//
//  - Call : onclick chat submit button
//////////////////////////////////////////////////
function sendMessage() {

    // Gets the input field, the message id and the new text inside the input
    let input = document.getElementById("chat-message-text");
    let messageId = input.getAttribute("id-message");
    let msg = input.value;

    // If its a new message
    if(messageId == null || messageId == "") { 


    // If it's an update
    } else {

        // Calls updateMessageText.php by giving the the message id and the new text
        $.ajax({
            type: 'POST',
            url: '../functions/updateMessageText.php',
            data: {
                id: messageId,
                text: msg
            },

            // After the call is done, if it went fine:
            // Empty the input, remove the 'id-message' attribute and updates the message text
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


/////  OVERRIDE CHAT CACHE  //////////////////////
//  Updates the chat cache
//
//  - Call     : After messages updates (new, edit or delete)
//  - Arguments: The whole chat-box div inner html
//////////////////////////////////////////////////
function overrideChatCache(chatContent) {

    // Calls the chat update function inside the cache.php file
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