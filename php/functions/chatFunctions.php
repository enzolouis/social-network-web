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
                !empty($_POST["message"]) &&
                !empty($_POST["newGroup"])) {
                    $pdo = createConnection();
                    $message = getLastMessage($pdo, $_POST["loggedUser"], $_POST["otherUser"]);
                    $messageId = addMessage($pdo, $_POST["loggedUser"], $_POST["otherUser"], date("Y-m-d"), date("H:i:s"), $_POST["message"], false);
                    if ($messageId != null) {
                        $separator = addSeparator($message, new DateTime(), date("H:i:s"), "user_me", true);
                        $newMessage = addChatMessage("user_me", $messageId, $_POST["message"], new DateTime(), date("H:i:s"));
                        if ($_POST["newGroup"] == "true" || $separator) {
                            $separatorToken = empty($separator) ? "no_separator:" : "separator";
                            $imageURL = 'src = "' . getUserByLogin($pdo, $_POST["loggedUser"])->getProfilePicture() .'"';
                            echo $separatorToken . addMessageGroup("user_me", $imageURL, $separator, $newMessage);
                        } else {
                            echo "no_separator:" . $newMessage;
                        }
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
                !empty($requestData["message"])) {
                $pdo = createConnection();
                echo editMessage($pdo, $requestData["messageId"], $requestData["message"]);
            }
            break;
    }

    function getParameters() : array {
        $postedData = file_get_contents('php://input');
        parse_str($postedData, $parsedData);
        return $parsedData;
    }
        