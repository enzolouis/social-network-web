<?php 
    require("../functions/chatDisplay.php");
    require("../functions/databaseFunctions.php");

    $user = null;
    if (isSessionValid()) {
        $user = $_SESSION["user"];
    } else {
        disconnect(); 
    }

    $pdo = createConnection();
?>

<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $user->getUsername().' - Chats' ?></title>

        <script defer src="https://use.fontawesome.com/releases/v6.4.2/js/all.js"></script>
        <script src = "https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
        <link rel = "stylesheet" href = "../../css/chat.css">

        <meta charset = "utf-8">
        <meta name = "language" content = "EN">
    </head>

    <body>
        <!-- The side bar menu -->
        <?php include("header.html"); ?>

        <main>

            <!-- Chats feed (all the available discussions) -->
            <div id = "discussions">
                <div id = "discussions-header">
                    <div id = "user-search">
                        <input type="text" id="user-search-input" placeholder="Search user..." autocomplete="off">
                        <div class='chat-option-separator'></div>
                        <i class="chat-option fa-solid fa-magnifying-glass"></i>
                    </div>
                    <div id="found-users">
                        <div id="no-user-found">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            <p>Couldn't find any user...<br>
                                <span>We tried our best</span>
                            </p>
                        </div>
                    </div>
                </div>
                <div id="contacted-users">
                    <?php echo getDiscussions($pdo, $user->getLogin()); ?>
                </div>
            </div>

            <!-- W in the shaaat -->
            <div id = "chat">

                <!-- Chat header  -->
                <div class = "contacted-user" id = "chat-header">
                    <!-- Chat header 
                    <button class="button primary-button" id="follow-button" onclick="follow()"><i class="fa-regular fa-star"></i><span>Follow</span></button>
                    <button class="button secondary-button"><i class="fa-regular fa-star"></i><span>Following</span></button>
                    <button class="button tertiary-button"><i class="fa-regular fa-star"></i><span>Infos</span></button>
                    <button class="button primary-danger-button"><i class="fa-regular fa-star"></i><span>Block</span></button>
                    <button class="button secondary-danger-button"><i class="fa-regular fa-star"></i><span>Unblock</span></button> -->
                </div>

                <script>
                    function follow() {
                        let followButton = document.getElementById("follow-button");
                        followButton.children[0].classList.remove("fa-regular")
                        followButton.children[0].classList.add("fa-solid")
                        followButton.onclick = function() {unfollow()};
                        followButton.style.transform = "scale(1.05)"
                        setTimeout(() => {
                            followButton.classList.remove("primary-button");
                            followButton.classList.add("secondary-button");
                            followButton.style.transform = "none"},
                        100);
                        followButton.children[1] .innerHTML = 'Following';
                    }

                    function unfollow() {
                        let followButton = document.getElementById("follow-button");
                        followButton.children[0].classList.remove("fa-solid")
                        followButton.children[0].classList.add("fa-regular")
                        followButton.onclick = function() {follow()};
                        followButton.style.transform = "scale(1.05)"
                        setTimeout(() => {
                            followButton.classList.remove("secondary-button");
                            followButton.classList.add("primary-button");
                            followButton.style.transform = "none"},
                        100);
                        followButton.children[1] .innerHTML = 'Follow';
                    }
                </script>

                <!-- Chat box -->
                <div id = "chat-box">
                    <div id = "chat-popup">Message deleted</div>
                </div>

                <!-- Message input -->
                <div id = "chat-message">
                    <div id = "chat-inputs">
                        <input type = "text" id = "chat-message-text" name = "text" placeholder="Enter message..." autocomplete="off">
                        <i class="chat-option fa-solid fa-circle-xmark" id = "chat-message-edit" onclick = "stopEdit()"></i>
                        <div class='chat-option-separator'></div>
                        <i class="chat-option fa-solid fa-image"></i>
                        <div class='chat-option-separator'></div>
                        <i class="chat-option fa-solid fa-face-smile"></i>
                        <div class='chat-option-separator'></div>
                        <i class="chat-option fa-solid fa-paper-plane" id = "chat-message-submit" onclick = "sendMessage()"></i>
                    </div>
                </div>
            </div>

            <div style="background:white;flex:1 0 10%;border-left:rgb(222, 222, 222) solid 1px">
            </div>

        </main>
    </body>

    <script src="../../js/chatDisplay.js"></script>
    <script src="../../js/chat.js"></script>
    <script src="../../js/chatSocket.js"></script>
    <script src="../../js/searchUser.js"></script>
</html> 