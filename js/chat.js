/////  EDIT MESSAGE  /////////////////////////////
//  Gets the selected message's text and puts it inside the input field
//  Plus adds a 'id-message' attribute to the input, used to know later
//  if we're in the middle of an edit or a normal message
//
//  - Call     : onclick edit button (hover message)
//  - Arguments: messageId - self explanatory
//////////////////////////////////////////////////
var editedMessage;
function editMessage(messageId) {

    // Gets the message and the input field
    let message = document.getElementById(messageId);
    let input   = document.getElementById("chat-message-text");
    let editBtn = document.getElementById("chat-message-edit");
    let form    = document.getElementById("chat-inputs");
    editedMessage = message.getElementsByClassName("msg-text")[0].innerHTML;

    input.focus();
    editBtn.style.display = "block";
    form.classList.add("editing");
    message.classList.add("editing");

    input.setAttribute("id-message", messageId);
    input.value = editedMessage;
    enableMessageSubmitButton();
}

/** 
 * Pastes a message into the clipboard
 * 
 * @param {number} messageId - The copied message's ID
 */
function copyMessage(messageId) {
    let message = document.getElementById(messageId);
    let messageContent = message.getElementsByClassName("msg-text")[0].innerHTML;
    navigator.clipboard.writeText(messageContent);
}

/** 
 * Deletes a message, both from the display and the database with an AJAX call
 * 
 * @param {number} messageId - the deleted message's ID
 */
function deleteMessage(messageId) {

    let message = document.getElementById(messageId);

    // Prevents the user from clicking multiple times on the delete option or any other option
    // while the message is being deleted
    message.getElementsByClassName("msg-options")[0].style.pointerEvents = "none";

    $.ajax({
        type: 'DELETE',
        url: '../functions/chatFunctions.php',
        data: {
            messageId: messageId,
        },
        success: function(data) {
            if (data == true) {  
                // Notify the other user
                sendToServer(Format.formatDelete(messageId));
                // If the message has been deleted, removes it from the chat and caches the result
                deleteMessageAndUpdateSeparators(message);
                overrideChatCache(document.getElementById("chat-box").innerHTML);
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
    let input = document.getElementById("chat-message-text");
    let messageId = input.getAttribute("id-message");
    let message = input.value;

    let canSendMessage = canSend;
    disableMessageSubmitButton();
    input.value = "";

    let msgWithoutSpaces = message.replace(' ', '');

    // If it's a new message
    if (!messageId && msgWithoutSpaces.length > 0 && canSendMessage) { 
        let lastMessageGroup = document.querySelectorAll(".msg-group");
        lastMessageGroup = lastMessageGroup[lastMessageGroup.length - 1];
        newGroup = lastMessageGroup.classList.contains("user_other");
        console.log("On doit crée un nouveau group ? " + newGroup);
        $.ajax({
            type: 'POST',
            url: '../functions/chatFunctions.php',
            data: {
                loggedUser: loggedUser,
                otherUser: otherUser,
                message: message,
                newGroup: newGroup,
            },
            success: function(data) {
                if (data) {
                    console.log("%c SUCCES: Update message", "color:green;");
                    
                    addNewMessageOrGroup(newGroup, data, lastMessageGroup);

                    // Send the received message to the other user
                    data = replaceUserMeWithUserOther(data);
                    sendToServer(Format.formatSend(data));

                    overrideChatCache(document.getElementById("chat-box").innerHTML);
                    loadContactedUsers();
                    scrollDownChat();
                } else {
                    console.log("%c ERREUR: Update message", "color:red;");
                }
            }
        })
    // If it's an edit
    } else if (messageId && msgWithoutSpaces.length > 0) {
        if (editedMessage !== message) {
            $.ajax({
                type: 'PATCH',
                url: '../functions/chatFunctions.php',
                data: {
                    messageId: messageId,
                    message: message
                },
                // After the call is done, if it went fine,
                // Empty the input, remove the 'id-message' attribute and updates the message text
                success: function(data) {
                    if (data) {
                        console.log("%c SUCCES: Update message", "color:green;");
    
                        document.getElementById(messageId).getElementsByClassName("msg-text")[0].innerHTML = message;
                        sendToServer(Format.formatEdit(messageId, message));
    
                        overrideChatCache(document.getElementById("chat-box").innerHTML);
                        stopEdit();
                        loadContactedUsers();
                    } else {
                        console.log("%c ERREUR: Update message", "color:red;");
                    }
                }
            })
        } else {
            stopEdit();
        }
    }
}

function addNewMessageOrGroup(newGroup, message, lastMessageGroup) {
    // If the last message is sent by the logged user and a new separator hasn't been inserted
    if (!newGroup && !hasSeparator(message)) {
        console.log(message);
        console.log("Meme groupe");
        // Append the new message to the current messages group
        lastMessageGroup.querySelector(".msg-group-messages").innerHTML += formatReceivedMessage(message);
    } else {
        console.log("Nouveau groupe");
        console.log(message);
        // Else, append the newly created messages group to the chat
        document.getElementById("chat-box").innerHTML += formatReceivedMessage(message);
    }
}

/**
 * Gets the message input back to a normal state, leaving "Edit message" state
 */
function stopEdit() {
    let input    = document.getElementById("chat-message-text");
    let editBtn  = document.getElementById("chat-message-edit");
    let editings = document.getElementsByClassName("editing");

    while (editings.length) {
        editings[0].classList.remove("editing");
    }

    input.removeAttribute("id-message");
    input.value = "";
    editBtn.style.display = "none";
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

function hasSeparator(message) {
    let firstSemiColon = message.indexOf(":");
    return message.substring(0, firstSemiColon) === "separator";
} 

function formatReceivedMessage(message) {
    let firstSemiColon = message.indexOf(":") + 1;
    return message.substring(firstSemiColon, message.length);
} 

/**
 * Configure the chat send message input
 */
var canSend = false;
let chatInput = document.getElementById("chat-message-text");

chatInput.addEventListener("keydown", function(e) {
    if (e.key == "Enter" && canSend) {
        sendMessage();
    }
});

chatInput.addEventListener("input", function() {
    if (this.value && !canSend) {
        enableMessageSubmitButton();
    } else if (!this.value) { 
        disableMessageSubmitButton();
    }
});

function enableMessageSubmitButton() {
    let chatMessageSubmit = document.getElementById("chat-message-submit");
    chatMessageSubmit.style.color = "var(--light-purple)";
    chatMessageSubmit.style.pointerEvents = "auto";
    canSend = true;
}

function disableMessageSubmitButton() {
    let chatMessageSubmit = document.getElementById("chat-message-submit");
    chatMessageSubmit.style.color = "var(--gray-02)";
    chatMessageSubmit.style.pointerEvents = "none";
    canSend = false;
}