<?php
    require("messageDAO.php");
    require("userDAO.php");

    function showHeader(PDO $pdo, User $other) : string {
        return '<h1>'.$other->getUsername().'</h1>
                <p>'.$other->getDescription().'</p>';
    }

    function showMessages(PDO $pdo, User $self, User $other) : string {
        $messages = getMessagesBetweenPeople($pdo, $self, $other);
        $result = "";
        
        if ($messages) {
            foreach ($messages as $message) {
                $id = ($message->getSender() == $self->getLogin()) ? "user_me" : "user_other";
                $result .= '<div class = "msg" id = "'.$id.'">
                                <p class = "msg-text">'.$message->getContent().'</p>
                                <p class = "msg-time">'.$message->getSentHour().'</p>
                            </div>';
            }
        }
        
        return $result;
    }

    function showChats(PDO $pdo, User $user) : string {
        $users = getContactedUsers($pdo, $user);
        $result = "";
        
        if ($users) {
            foreach ($users as $contactedUser) {
                $result .= '<div class = "contacted-user" id = "'. $contactedUser->getLogin() .'">
                                <div class = "contacted-user-pfp-container">
                                    <img class = "contacted-user-pfp">
                                    <div class = "contacted-user-status"></div>
                                </div>
                                <div class = "contacted-user-infos">
                                    <label class = "contacted-user-name">'.$contactedUser->getUsername().'</label>
                                    <p class = "contacted-user-description">'.$contactedUser->getDescription().'</p>
                                </div>
                            </div>';
            }
        }
        return $result;
    }

?>