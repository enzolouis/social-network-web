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
        <script defer src="https://use.fontawesome.com/releases/v6.4.2/js/all.js"></script>
        <link rel = "stylesheet" href = "../../css/chat.css">
    </head>    
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <body>
        <?php include("header.html"); ?>
        <main>

            <div id = "discussions">
                <div id = "discussions-header">
                    <h1>Chats</h1>
                    <p>Test</p> 
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
                        <input type = "text" id = "chat-message-text" name = "text" placeholder="Enter message...">
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
</html> 

<?php //disconnect(); ?>