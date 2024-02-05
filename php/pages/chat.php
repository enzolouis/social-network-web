<?php 
    require("../functions/chatDisplay.php");
    require("../functions/databaseFunctions.php");
    session_start();
    $me = $_SESSION["user"];
    $pdo = createConnection();
?>

<!DOCTYPE html>
<html>
    <head>
        <link rel = "stylesheet" href = "../../css/chat.css">
    </head>    
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <body>
        <main>

            <div id = "discussions">
                <p>Discussions</p>
                <?php echo showChats($pdo, $me->getLogin()); ?>
            </div>

            <!-- W in the shaaat -->
            <div id = "chat">

                <!-- Chat header  -->
                <div class = "contacted-user" id = "chat-header">
                    <img src="../images/loading.gif" class="loading-gif" style="display: none;" width="200px">
                </div>

                <!-- Chat box -->
                <div id = "chat-box">
                    <img src="../images/loading.gif" class="loading-gif" style="display: none;" width="200px">
                </div>

                <!-- Message input -->
                <div id = "chat-message">
                    <form action = "#">
                        <input type = "text"   id = "chat-message-text"   name = "text">
                        <input type = "submit" id = "chat-message-submit" name = "submit">
                    </form>
                </div>
            </div>

            <div id = "emotes">
                <p>Emotes</p>
            </div>

        </main>
    </body>

    <!-- Scroll down the chat -->
    <script>
        var chat = document.getElementById("chat-box");
        chat.scrollTop = chat.scrollHeight;
    </script>

    
    <script src="../../js/chat.js"></script>
</html> 