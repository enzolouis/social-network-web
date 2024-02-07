<?php 

    function cacheChat($currentUser, $otherUser, $content) {
        $sessionVarName = "user:" . $currentUser . ':' . $otherUser;
        $_SESSION[$sessionVarName] = $content;
    }

    function cacheHeader($otherUser, $content) {
        $sessionVarName = "header:" . $otherUser;
        $_SESSION[$sessionVarName] = $content;
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (!empty($_POST["otherUser"]) && !empty($_POST["content"])) {
            $otherUser = $_POST["otherUser"];
            $content = $_POST["content"];
            if (!empty($_POST["currentUser"])) {
                cacheChat($_POST["currentUser"], $otherUser, $content);
            } else {
                cacheHeader($otherUser, $content);
            }
        }
    }