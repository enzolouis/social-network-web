<?php
    require("functions.php");
    require("chatDisplay.php");
    $pdo = createConnection();

    echo showChatMessages($pdo, $_POST["user"], $_POST["otherUser"]);