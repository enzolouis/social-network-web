var loggedUser;
var otherUser;

/**
 * Loads both the chat header and the chat messages between the logged and an other user
 * Also initializes the WebSocket between the logged user and the chatted user
 * 
 * @param {string} login 
 * @param {string} otherLogin 
 */
function loadHeaderAndChat(login, otherLogin) {
    loggedUser = login;
    otherUser = otherLogin;
    chatSocket = new WebSocket("ws://localhost:8080?from=" + loggedUser + "&to="+otherUser);

    chatSocket.onopen = function(e) {
        console.log("------------ Connection established! ------------");
    };

    chatSocket.onmessage = function(e) {
        let chat = document.getElementById("chat-box")

        const msg = e.data;

        const mtype = Format.getMessageType(msg);
        console.log(mtype)
        switch (mtype) {
            case "send":
                //console.log("SEND?id=null&content=" + Format.getContentFromSendMessage(e.data))
                let lastMessageGroup = document.querySelectorAll(".msg-group");
                lastMessageGroup = lastMessageGroup[lastMessageGroup.length - 1];
                newGroup = lastMessageGroup.classList.contains("user_me");
                addNewMessageOrGroup(newGroup, Format.getContentFromSendMessage(msg), lastMessageGroup);
                removeSentMessageOptions();
                overrideChatCache(document.getElementById("chat-box").innerHTML);
                break;
            case "edit":
                //console.log("EDIT?id=" + Format.getIdFromEditMessage() + "&content=" + Format.getContentFromEditMessage(msg))
                document.getElementById(Format.getIdFromEditMessage(msg)).getElementsByClassName("msg-text")[0].innerHTML = Format.getContentFromEditMessage(msg);
                break;
            case "dele":
                console.log("DELE?id=" + Format.getIdFromDeleteMessage(msg))
                deleteMessageAndUpdateSeparators(document.getElementById(Format.getIdFromEditMessage(msg)));
                overrideChatCache(document.getElementById("chat-box").innerHTML);
                break;
            default:
                console.log("bug");
        }
    };

    let contactedUser = document.getElementById(otherLogin);
    let contactedUserOnClick = contactedUser.onclick;
    contactedUser.onclick = null;
    loadChatHeader(otherLogin);
    loadChat(login, otherLogin);
    $(document).ajaxStop(function(){
        contactedUser.onclick = contactedUserOnClick;
    });
}

/**
 * Removes every child of an element except the first X specified children
 * 
 * @param {Object} element 
 * @param {number} numberOfChildrenKept 
 */
function removeChildrenExceptFirst(element, numberOfChildrenKept = 1) {
    while (element.childElementCount > numberOfChildrenKept) {
        element.removeChild(element.lastChild);
    }
}

/**
 * Loads every messages between the logged and an other user with an AJAX call
 * 
 * @param {string} login 
 * @param {string} otherLogin 
 */
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

/** 
 * Loads the chat header with the other user's informations with an AJAX call
 * 
 * @param {string} otherLogin 
 */
function loadChatHeader(otherLogin) {
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

/**
 * Loads every contacted users with an AJAX call
 */
function loadContactedUsers() {
    let contactedUsers = document.getElementById("contacted-users");
    $.ajax({
        type: 'GET',
        url: '../functions/chatDisplayCall.php',
        data: {
            section: "discussions",
            loggedUser: loggedUser,
        },
        success: function(data) {
            contactedUsers.innerHTML = data;
        }
    })
}

/** 
 * Scrolls down the chat
 */
function scrollDownChat() {
    let chat = document.getElementById("chat-box");
    chat.scrollTop = chat.scrollHeight;
}

function replaceUserMeWithUserOther(message) {
    return message.replace(/user_me/g, 'user_other');
}

/**
 * Removes a message and updates the above separator accordingly
 * 
 * @param {HTMLElement} message     The deleted message HTMLElement
 */
function deleteMessageAndUpdateSeparators(message, fromOtherUser = false) {
    let loggedUserClass, otherUserClass;
    if (!fromOtherUser) {
        loggedUserClass = "user_me";
        otherUserClass = "user_other";
    } else {
        loggedUserClass = "user_other";
        otherUserClass = "user_me";
    }

    let chat = document.getElementById("chat-box");
    let messageContainer = message.parentElement;

    // First, check if this was the last message of the messages group
    if (messageContainer.childElementCount == 1) {

        // Get the previous and next sibling of the message group
        let previousSibling = messageContainer.parentElement.previousElementSibling;
        let nextSibling = messageContainer.parentElement.nextElementSibling;
        let previousSiblingClasses;
        let nextSiblingClasses;
        if (previousSibling) { 
            previousSiblingClasses = previousSibling.classList; 
        } 
        if (nextSibling) { 
            nextSiblingClasses = nextSibling.classList; 
        } 

        // Check if the previous sibling is a separator
        if (previousSiblingClasses && (previousSiblingClasses.contains("date-separator") || previousSiblingClasses.contains("hour-separator"))) {

            // Check if the following sibling is also a separator or doesn't exist
            if ((nextSiblingClasses && (nextSiblingClasses.contains("date-separator") || nextSiblingClasses.contains("hour-separator")))
                || !nextSibling) {
                // If so, just remove the deleted message, nothing more needed
                chat.removeChild(previousSibling);
            } else if (nextSibling) {
                // If not, we have to update the above separator to match the message
                // following the one we're deleting
                // First, we retrieve the following message's hour
                nextMessage = nextSibling.children[1].firstChild;
                messageHour = nextMessage.getAttribute("id-hour");

                // Next, we check if the next message was sent from the logged user or another user
                if (nextSiblingClasses.contains(otherUserClass)) {
                    // If from another user, we have to update our separator to appear on the other user's side
                    previousSiblingClasses.remove(loggedUserClass);
                    previousSiblingClasses.add(otherUserClass);
                }

                // Then, we check whether the previous separator was a date or hour separator,
                // and update the separator's content accordingly
                if (previousSiblingClasses.contains("date-separator")) {
                    previousSibling.innerHTML = "Date - " + messageHour;
                } else {
                    previousSibling.innerHTML = messageHour;
                }
            } 
        }

        // Remove the deleted message
        chat.removeChild(messageContainer.parentElement);

    // Else, if the deleted message wasn't the last of the messages group
    } else {
        let previousSibling = messageContainer.parentElement.previousElementSibling;
        let firstMessageHour = messageContainer.firstChild.getAttribute("id-hour");

        // Check if the previous sibling is a date or hour separator, and update the hour with 
        // the first message of the messages group accordingly
        if (previousSibling.classList.contains("date-separator")) {
            previousSibling.innerHTML = "Date - " + firstMessageHour;
        } else if (previousSibling.classList.contains("hour-separator")) {
            previousSibling.innerHTML = firstMessageHour;
        }
        // Remove the deleted message
        messageContainer.removeChild(message);
    }
}


function removeSentMessageOptions() {
    let lastMessage = document.querySelectorAll(".msg");
    lastMessage = lastMessage[lastMessage.length - 1];
    removeChildrenExceptFirst(lastMessage.querySelector(".msg-options"), 3);
}