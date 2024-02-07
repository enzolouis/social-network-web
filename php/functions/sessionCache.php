<?php 
    session_start();

    /**
     * Caches the new chat content for the given contacted user
     *
     * @param string $currentUser   Currently logged in user
     * @param string $otherUser     Contacted user
     * @param string $content       Chat content
     * @return void
     */
    function cacheChat(string $currentUser, string $otherUser, string $content) {
        $sessionVarName = "user:" . $currentUser . ':' . $otherUser;
        $_SESSION[$sessionVarName] = $content;
    }

    /**
     * Caches the new header content for the given contacted user
     *
     * @param string $otherUser     Contacted user
     * @param string $content       Header content
     * @return void
     */
    function cacheHeader(string $otherUser, string $content) {
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