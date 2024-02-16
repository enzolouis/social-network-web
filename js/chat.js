var currentUser;
var otherUser;

var conn;

function sendToServer(data) {
    conn.send(data);
}

class Format {
    static formatSend(content) {
        return ":send: " + content;
    }

    static formatDelete(id) {
        return ":dele|" + id + ":";
    }

    static formatEdit(id, newcontent) {
        return ":edit|" + id + ": " + newcontent;
    }

    static getMessageType(content) {
        if (content.startsWith(":dele")) {
            return "dele";
        } else if (content.startsWith(":edit")) {
            return "edit";
        } else if (content.startsWith(":send")) {
            return "send";
        } else {
            return "send";
        }
    }

    static getIdFromDeleteMessage(content) {
        let firstSemiColon = content.substring(1, content.length).indexOf(":") + 1;
        return content.substring(6, firstSemiColon);
    }

    static getContentFromSendMessage(content) {
        let firstSemiColon = content.substring(1, content.length).indexOf(":") + 1;
        return content.substring(firstSemiColon+2, content.length);
    }

    static getIdFromEditMessage(content) {
        let firstSemiColon = content.substring(1, content.length).indexOf(":") + 1;
        console.log("originalcontent="+content)
        console.log("id="+content.substring(6, firstSemiColon))
        console.log("firstsemicolonindex="+firstSemiColon)
        return content.substring(6, firstSemiColon);
    }

    static getContentFromEditMessage(content) {
        let firstSemiColon = content.substring(1, content.length).indexOf(":") + 1;
        return content.substring(firstSemiColon+2, content.length);
    }
}

function loadHeaderAndChat(login, otherLogin) {
    currentUser = login;
    otherUser = otherLogin;
    conn = new WebSocket("ws://localhost:8080?from=" + currentUser + "&to="+otherUser);

    conn.onopen = function(e) {
        console.log("------------ Connection established! ------------");
    };

    conn.onmessage = function(e) {
        let div = document.getElementById("chat-box")

        const msg = e.data;

        const mtype = Format.getMessageType(msg);
        console.log(mtype)
        switch (mtype) {
            case "send":
                //console.log("SEND?id=null&content=" + Format.getContentFromSendMessage(e.data))
                div.innerHTML += Format.getContentFromSendMessage(msg);
                break;
            case "edit":
                //console.log("EDIT?id=" + Format.getIdFromEditMessage() + "&content=" + Format.getContentFromEditMessage(msg))
                document.getElementById(Format.getIdFromEditMessage(msg)).getElementsByClassName("msg-text")[0].innerHTML = Format.getContentFromEditMessage(msg);
                break;
            case "dele":
                console.log("DELE?id=" + Format.getIdFromDeleteMessage(msg))
                document.getElementById("chat-box").removeChild(document.getElementById(Format.getIdFromEditMessage(msg)));
                break;
            default:
                console.log("bug");
        }
        scrollDownChat();
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
        type: 'POST',
        url: '../functions/showMessages.php',
        data: {
            cacheOnly: "true",
            user: login,
            otherUser: otherLogin,
        },
        success: function(data) {
            removeChildrenExceptFirst(chat);
            chat.innerHTML += data;
            scrollDownChat();
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
                        removeChildrenExceptFirst(chat);
                        chat.innerHTML += data;
                        scrollDownChat();
                    }
                }
            })
        }
    })
}

function loadHeader(otherLogin) {
    let chatHeader = document.getElementById("chat-header");
    $.ajax({
        type: 'POST',
        url: '../functions/showHeader.php',
        data: {
            cacheOnly: "true",
            otherUser: otherLogin,
        },
        success: function(data) {
            chatHeader.innerHTML = data;
            $.ajax({
                type: 'POST',
                url: '../functions/showHeader.php',
                data: {
                    cacheOnly: "false",
                    otherUser: otherLogin,
                },
                success: function(data) {
                    if (data != "No changes") {
                        chatHeader.innerHTML = data;
                    }
                    // chatHeader.innerHTML += '<button class="design-button-test"><i class="fa-regular fa-star"></i><span>Follow</span></button>'
                }
            })
        }
    })
}

function loadDiscussions() {
    $.ajax({
        type: 'POST',
        url: '../functions/showDiscussions.php',
        data: {
            currentUser: currentUser,
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

const sleep = ms => new Promise(r => setTimeout(r, ms));

function deleteMessage(messageId) {
    sendToServer(Format.formatDelete(messageId));

    let chat = document.getElementById("chat-box");
    chat.removeChild(document.getElementById(messageId));
    $.ajax({
        type: 'POST',
        url: '../functions/deleteMessage.php',
        data: {
            id: messageId,
        },
        success: function() {
            overrideChatCache(chat.innerHTML);
        }
    })
    document.getElementById("chat-popup").style.opacity = 1;
    setTimeout(() => document.getElementById("chat-popup").style.opacity = 0, "2000");
    
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
    if (!input.value)
        return;

    let messageId = input.getAttribute("id-message");
    let msg = input.value;
    let chat = document.getElementById("chat-box");

    // If its a new message
    if (!messageId) { 
        $.ajax({
            type: 'POST',
            url: '../functions/addMessage.php',
            data: {
                user: currentUser,
                otherUser: otherUser,
                content: msg,
            },

            success: function(data) {
                if (data) {
                    console.log("%c SUCCES: Update message", "color:green;");
                    document.getElementById("chat-box").innerHTML += data;
                    
                    data = replaceUserMeWithUserOther(data);
                    sendToServer(Format.formatSend(data));

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

                    stopEdit();
                    document.getElementById(messageId).getElementsByClassName("msg-text")[0].innerHTML = msg;
                    sendToServer(Format.formatEdit(messageId, msg));

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

function replaceUserMeWithUserOther(message) {
    return message.replace('user_me', 'user_other');
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
            currentUser: currentUser,
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