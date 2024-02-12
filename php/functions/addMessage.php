<?php
    session_start();
    require("databaseFunctions.php");
    require("chatDisplay.php");

    $pdo = createConnection();
    addMessage($pdo, $_POST["user"], $_POST["otherUser"], date("Y-m-d"), date("H:i:s"), $_POST["content"], false);
    echo addChatMessage("user_me", "", $_POST["content"]);