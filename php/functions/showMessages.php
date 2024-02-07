<?php
    session_start();
    require("databaseFunctions.php");
    require("chatDisplay.php");

    if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST["cacheOnly"])) {
        $sessionVarName = "user:" . $_POST["user"] . ':' . $_POST["otherUser"];
        if ($_POST["cacheOnly"] == "true") {
            if (empty($_SESSION[$sessionVarName])) {    
                $pdo = createConnection();
                $chatContent = showChatMessages($pdo, $_POST["user"], $_POST["otherUser"]);
                $_SESSION[$sessionVarName] = $chatContent;
            } 
            echo $_SESSION[$sessionVarName];
        } else {
            $pdo = createConnection();
            $chatContent = showChatMessages($pdo, $_POST["user"], $_POST["otherUser"]);
            if ($chatContent != $_SESSION[$sessionVarName]) {
                $_SESSION[$sessionVarName] = $chatContent;
                echo $_SESSION[$sessionVarName];
            } else {
                echo "No changes";
            }
        }
    }
    