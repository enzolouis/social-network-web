<?php
    session_start();
    require("../functions/chatDisplay.php");
    require("../functions/databaseFunctions.php");
    
    $requestMethod = $_SERVER['REQUEST_METHOD'];
    switch ($requestMethod) {
        // Case POST, called when the user sends a message
        case "POST":
            if (!empty($_POST["loggedUser"]) && 
                !empty($_POST["otherUser"]) &&
                !empty($_POST["message"])) {
                    $pdo = createConnection();
                    $messageId = addMessage($pdo, $_POST["loggedUser"], $_POST["otherUser"], date("Y-m-d"), date("H:i:s"), $_POST["content"], false);
                    if ($messageId != null) {
                        echo addChatMessage("user_me", $messageId, $_POST["content"]);
                    } else {
                        echo false;
                    }
            }
            break;
        // Case DELETE, called when the user deletes a message
        case "DELETE":
            $requestData = getParameters();
            if (!empty($requestData["messageId"])) {
                $pdo = createConnection();
                echo deleteMessage($pdo, $requestData["messageId"]);
            }
            break;
        // Case PATCH, called when the user edits a message
        case "PATCH":
            $requestData = getParameters();
            if (!empty($requestData["messageId"]) &&
                !empty($requestData["messageContent"])) {
                $pdo = createConnection();
                echo editMessage($pdo, $requestData["messageId"], $requestData["messageContent"]);
            }
            break;
    }

    function getParameters() : array {
        $postedData = file_get_contents('php://input');
        parse_str($postedData, $parsedData);
        return $parsedData;
    }
        