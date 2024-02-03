<?php
    require("messageDAO.php");
    require("userDAO.php");

    function showHeader($pdo, $other) : string{
        $result = "";
        $user = getUserById($pdo, $other);

        $result .= '<h1>'.$user->getUsername().'</h1>
                    <p>'.$user->getDescription().'</p>';

        return $result;
    }

    function showMessages($pdo, $self, $other) : string {
        $messages = getMessagesBetweenPeople($pdo, $self, $other);
        $result = "";
        
        foreach($messages as $message) {
            $id = ($message->getSender() == $self) ? "user_me" : "user_other";
            $result .= '<div class = "msg" id = "'.$id.'">
                            <p class = "msg-text">'.$message->getContent().'</p>
                            <p class = "msg-time">'.$message->getSentHour().'</p>
                        </div>';
        }
        
        return $result;
    }

?>