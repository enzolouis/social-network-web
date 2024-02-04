<?php
    require("functions.php");
    require("chatDisplay.php");
    $pdo = createConnection();
    $user = getUserById($pdo, $_POST["user"]);
    $otherUser = getUserById($pdo, $_POST["otherUser"]);

    echo showMessages($pdo, $user, $otherUser);