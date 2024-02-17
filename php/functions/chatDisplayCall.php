<?php
    session_start();
    require("../functions/chatDisplay.php");
    require("../functions/databaseFunctions.php");
    
    /* Case GET, when the user wants to retrieve information like :
        - the contacted users (discussions), 
        - the chat header (other user's informations),
        - the chat messages,
        - the found users after searching in the search bar
    */
    if ($_SERVER['REQUEST_METHOD'] == "GET") {
        if (!empty($_GET["section"])) {
            switch ($_GET["section"]) {
                case "discussions":
                    if (!empty($_GET["loggedUser"])) {
                        $pdo = createConnection();
                        echo getDiscussions($pdo, $_GET["loggedUser"]);
                    }
                    break;
                case "chatHeader":
                    if (!empty($_GET["otherUser"])) {
                        $sessionVarName = "userHeader:" . $_GET["otherUser"];
                        if (empty($_SESSION[$sessionVarName])) {
                            $pdo = createConnection();
                            $_SESSION[$sessionVarName] = getChatHeader($pdo, $_GET["otherUser"]);
                        }
                        echo $_SESSION[$sessionVarName];
                    }
                    break;
                case "chatMessages":
                    if (!empty($_GET["loggedUser"]) && !empty($_GET["otherUser"])) {
                        $sessionVarName = "user:" . $_GET["loggedUser"] . ':' . $_GET["otherUser"];
                        if (empty($_SESSION[$sessionVarName])) {
                            $pdo = createConnection();
                            $_SESSION[$sessionVarName] = getChatMessages($pdo, $_GET["loggedUser"], $_GET["otherUser"]);
                        }
                        echo $_SESSION[$sessionVarName];
                    }
                    break;
                case "foundUsers":
                    if (!empty($_GET["loggedUser"]) && !empty($_GET["searchedUser"])) {
                        $pdo = createConnection();
                        echo getFoundUsers($pdo, $_GET["loggedUser"], $_GET["searchedUser"]);
                    }
                    break;
            }
        }
    }