<?php
    session_start();
    require("databaseFunctions.php");
    require("chatDisplay.php");
    $sessionVarName = "user:" . $_POST["user"] . ':' . $_POST["otherUser"];
    if (empty($_SESSION[$sessionVarName])){
        $pdo = createConnection();
        $_SESSION[$sessionVarName] = showChatMessages($pdo, $_POST["user"], $_POST["otherUser"]);    
    } 
    echo $_SESSION[$sessionVarName];
    