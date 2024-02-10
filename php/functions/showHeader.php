<?php
    session_start();
    require("databaseFunctions.php");
    require("chatDisplay.php");

    if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST["cacheOnly"])) {
        $sessionVarName = "userHeader:" . $_POST["otherUser"];
        if ($_POST["cacheOnly"] == "true") {
            if (empty($_SESSION[$sessionVarName])){
                $pdo = createConnection();
                $_SESSION[$sessionVarName] = showChatHeader($pdo, $_POST["otherUser"]);
            } 
            echo $_SESSION[$sessionVarName];
        } else {
            $pdo = createConnection();
            $headerContent = showChatHeader($pdo, $_POST["otherUser"]);
            if ($headerContent != $_SESSION[$sessionVarName]) {
                $_SESSION[$sessionVarName] = $headerContent;
                echo $_SESSION[$sessionVarName];
            } else {
                echo "No changes";
            }
        }
    }