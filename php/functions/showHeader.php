<?php
    session_start();
    require("databaseFunctions.php");
    require("chatDisplay.php");
    $sessionVarName = "userHeader:" . $_POST["otherUser"];
    if (empty($_SESSION[$sessionVarName])){
        $pdo = createConnection();
        $_SESSION[$sessionVarName] = showChatHeader($pdo, $_POST["otherUser"]);
    } 
    echo $_SESSION[$sessionVarName];