<?php 
    require("chatDisplay.php");
    require("functions.php");

    $pdo = createConnection();
?>

<!DOCTYPE html>
<html>
    <head>
        <link rel = "stylesheet" href = "../css/chat.css">
    </head>    

    <body>
        <main>

            <div id = "discussions">
                <p>Discussions</p>
            </div>

            <!-- W in the shaaat -->
            <div id = "chat">

                <!-- Chat header  -->
                <div id = "chat-header">
                    <img id = "chat-header-pp" src = "https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_960_720.png">
                    <div id = "chat-header-infos">
                        <?php echo showHeader($pdo, "Nautilus"); ?>
                    </div>
                    
                </div>

                <!-- Chat box -->
                <div id = "chat-box">
                    <?php 
                        echo showMessages($pdo, "xouxou", "Nautilus");
                        echo showMessages($pdo, "Nautilus", "xouxou");
                    ?>
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

</html> 