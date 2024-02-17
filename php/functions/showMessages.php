<?php
    $start = microtime(true);
    session_start();
    require("databaseFunctions.php");
    require("chatDisplay.php");

    if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST["cacheOnly"])) {
        $sessionVarName = "user:" . $_POST["user"] . ':' . $_POST["otherUser"];
        if ($_POST["cacheOnly"] == "true") {
            if (empty($_SESSION[$sessionVarName])) {    
                $pdo = createConnection();
                $_SESSION[$sessionVarName] = getChatMessages($pdo, $_POST["user"], $_POST["otherUser"]);
            } 
            echo microtime(true) - $start;  
        } else {
            $pdo = createConnection();
            $chatContent = getChatMessages($pdo, $_POST["user"], $_POST["otherUser"]);
            if ($chatContent != $_SESSION[$sessionVarName]) {
                $_SESSION[$sessionVarName] = $chatContent;
                echo microtime(true) - $start;
            } else {
                echo microtime(true) - $start;
            }
        }
    }
    