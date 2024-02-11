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
        <script src = "http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
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
                </div>
                <?php echo showDiscussionsChats($pdo, $user->getLogin()); ?>
            </div>

            <!-- W in the shaaat -->
            <div id = "chat">

                <!-- Chat header  -->
                <div class = "contacted-user" id = "chat-header">
                </div>

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

        </main>
    </body>

    <script src="../../js/chat.js"></script>
    <script src="../../js/searchUser.js"></script>
</html> 