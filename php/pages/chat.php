<?php 
    require("../functions/chatDisplay.php");
    require("../functions/databaseFunctions.php");

    $user = null;
    if(isSessionValid()) {
        $user = $_SESSION["user"];
    } else {
        disconnect(); 
    }
    $other = new User('xouxou', 'Maxence Maury-Balit', 'xxx', 'Wise mystical tree enjoyer');

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
                    <img src="../../images/loading.gif" class="loading-gif" style="display: none;" width="200px">
                </div>

                <!-- Chat box -->
                <div id = "chat-box">
                    <img src="../../images/loading.gif" id="loading-gif" style="display: none;" width="200px">
                </div>

                <!-- Message input -->
                <div id = "chat-message">
                    <form action = "#">
                        <input type = "text" id = "chat-message-text" name = "text">
                        <button type = "button" id = "chat-message-submit" onclick = "sendMessage()">Envoyer</button>
                    </form>
                </div>
            </div>

        </main>
    </body>

    <script src="../../js/chat.js"></script>

    <!-- Scroll down the chat -->    
    <script>
        var chat = document.getElementById("chat-box");
        chat.scrollTop = chat.scrollHeight;
    </script>
</html> 

<?php //disconnect(); ?>