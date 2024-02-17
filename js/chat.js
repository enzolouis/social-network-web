var loggedUser;
var otherUser;

var conn;

function sendToServer(data) {
    conn.send(data);
}

function replaceUserMeWithUserOther(message) {
    return message.replace('user_me', 'user_other');
}

function loadHeaderAndChat(login, otherLogin) {
    loggedUser = login;
    otherUser = otherLogin;
    conn = new WebSocket("ws://localhost:8080?from=" + loggedUser + "&to="+otherUser);

    conn.onopen = function(e) {
        console.log("------------ Connection established! ------------");
    };

    conn.onmessage = function(e) {
        let div = document.getElementById("chat-box")
        div.innerHTML += "<div>"+e.data+"</div>"
    };

    let contactedUser = document.getElementById(otherLogin);
    let contactedUserOnClick = contactedUser.onclick;
    contactedUser.onclick = null;
    loadHeader(otherLogin);
    loadChat(login, otherLogin);
    $(document).ajaxStop(function(){
        contactedUser.onclick = contactedUserOnClick;
    });
}

function removeChildrenExceptFirst(element, numberOfChildrenKept = 1) {
    while (element.childElementCount > numberOfChildrenKept) {
        element.removeChild(element.lastChild);
    }
}

function loadChat(login, otherLogin) {
    let chat = document.getElementById("chat-box");
    $.ajax({
        type: 'GET',
        url: '../functions/chatDisplayCall.php',
        data: {
            section: "chatMessages",
            loggedUser: login,
            otherUser: otherLogin,
        },
        success: function(data) {
            chat.innerHTML = data;
            scrollDownChat();
        }
    })
}

function loadHeader(otherLogin) {
    let chatHeader = document.getElementById("chat-header");
    $.ajax({
        type: 'GET',
        url: '../functions/chatDisplayCall.php',
        data: {
            section: "chatHeader",
            otherUser: otherLogin,
        },
        success: function(data) {
            chatHeader.innerHTML = data;
        }
    })
}

function loadDiscussions() {
    $.ajax({
        type: 'GET',
        url: '../functions/chatDisplayCall.php',
        data: {
            section: "discussions",
            loggedUser: loggedUser,
        },
        success: function(data) {
            updateDiscussionContent(data);
        }
    })
}

function updateDiscussionContent(newContent) {
    let discussions = document.getElementById("discussions");
    let userSearchInput = document.getElementById("user-search-input");
    
    let userInputValue = userSearchInput.value;
    removeChildrenExceptFirst(discussions);
    discussions.innerHTML += newContent;
    
    userSearchInput = document.getElementById("user-search-input");
    userSearchInput.value = userInputValue;
    addEventListenersFoundUsers();
    addEventListenersSearchInput();
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
    let input   = document.getElementById("chat-message-text");
    let editBtn = document.getElementById("chat-message-edit");
    let form    = document.getElementById("chat-inputs");

    input.focus();
    editBtn.style.display = "block";
    form.classList.add("editing");
    message.classList.add("editing");

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
    let message = document.getElementById(messageId);
    // Prevents the user from clicking multiple time on the delete option or any other option
    message.getElementsByClassName("msg-options")[0].style.pointerEvents = "none";
    $.ajax({
        type: 'DELETE',
        url: '../functions/chatFunctions.php',
        data: {
            messageId: messageId,
        },
        success: function(data) {
            if (data == true) {  
                //  If the message has been deleted, removes it from the chat and caches the result
                chat.removeChild(message);
                overrideChatCache(chat.innerHTML);
            } else {
                // If the message has not been deleted, restores the pointer events on the message options
                message.getElementsByClassName("msg-options")[0].style.pointerEvents = "auto";
            }
        }
    })
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
    let chat = document.getElementById("chat-box");

    let msgWithoutSpaces = msg.replace(' ', '');
    // If its a new message
    if (!messageId && msgWithoutSpaces.length > 0) { 
        $.ajax({
            type: 'POST',
            url: '../functions/addMessage.php',
            data: {
                user: loggedUser,
                otherUser: otherUser,
                content: msg,
            },

            success: function(data) {
                if (data) {
                    console.log("%c SUCCES: Update message", "color:green;");
                    document.getElementById("chat-box").innerHTML += data;
                    replaceUserMeWithUserOther(data);
                    sendToServer(data);

                    chat = document.getElementById("chat-box").innerHTML;
                    overrideChatCache(chat);
                    input.value = "";
                } else {
                    console.log("%c ERREUR: Update message", "color:red;");
                }
                input.value = "";
                loadDiscussions();
                scrollDownChat();
            }
        })
    // If it's an update
    } else if (msgWithoutSpaces.length > 0) {

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

                    stopEdit();
                    document.getElementById(messageId).getElementsByClassName("msg-text")[0].innerHTML = msg;

                    chat = document.getElementById("chat-box").innerHTML;
                    overrideChatCache(chat);
                } else {
                    console.log("%c ERREUR: Update message", "color:red;");
                }
                loadDiscussions();
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
    // Calls the chat update function inside the sessionCache.php file
    $.ajax({
        type: 'POST',
        url: '../functions/sessionCache.php',
        data: {
            currentUser: loggedUser,
            otherUser: otherUser,
            content: chatContent
        },
        success: function(data) {
            if(data) console.log("%c SUCCESS: Cache chat override", "color:green;");
            else     console.log("%c ERROR: Cache chat override", "color:red;");
        }
    })
}


function scrollDownChat() {
    let chat = document.getElementById("chat-box");
    chat.scrollTop = chat.scrollHeight;
}


function stopEdit() {
    let input    = document.getElementById("chat-message-text");
    let editBtn  = document.getElementById("chat-message-edit");
    let editings = document.getElementsByClassName("editing");

    while(editings.length) {
        editings[0].classList.remove("editing");
    }

    input.removeAttribute("id-message");
    input.value = "";
    editBtn.style.display = "none";
}


let chatInput = document.getElementById("chat-message-text");
chatInput.addEventListener("keydown", function(e) {
    if (e.key == "Enter") {
        sendMessage();
    }
});