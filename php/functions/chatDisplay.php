<?php
    require("messageDAO.php");
    require("userDAO.php");

    // 
    function showDiscussionsHeader(PDO $pdo, string $user) : string {
        return "";
    }

    // 
    function showDiscussionsChats(PDO $pdo, string $user) : string {
        $users = getContactedUsers($pdo, $user);
        $result = "";
        
        if ($users) {
            foreach ($users as $contactedUser) {
                $result .= '<div class = "contacted-user" id = "'. $contactedUser->getLogin() .'" onclick="loadChat(\'' .$user. '\', this.id)">
                                <div class = "contacted-user-pfp-container">
                                    <img class = "contacted-user-pfp">
                                    <div class = "contacted-user-status"></div>
                                </div>
                                <div class = "contacted-user-infos">
                                    <p class = "contacted-user-name">'.$contactedUser->getUsername().'</p>
                                    <p class = "contacted-user-description">'.$contactedUser->getDescription().'</p>
                                </div>
                            </div>';
            }
        }
        return $result;
    }

    // 
    function showChatHeader(PDO $pdo, string $other) : string {
        $user = getUserById($pdo, $other);

        return '<div class = "contacted-user-pfp-container">
                    <img class = "contacted-user-pfp">
                    <div class = "contacted-user-status"></div>
                </div>
                <div class = "contacted-user-infos">
                    <p class = "contacted-user-name">'.$user->getUsername().'</p>
                    <p class = "contacted-user-description">'.$user->getDescription().'</p>
                </div>';
    }

    // 
    function showChatMessages(PDO $pdo, string $self, string $other) : string {
        $messages = getMessagesBetweenPeople($pdo, $self, $other);
        $result = "";
        
        if ($messages) {
            $previousSentDate = $messages[0]->getSentDate();
            $previousSentHour = $messages[0]->getSentHour();
            foreach ($messages as $message) {
                if (getTimeDifferenceInHours($message->getSentDate(), $message->getSentHour(), $previousSentDate, $previousSentHour) > 10) {
                    $messageDate = $message->getSentDate()->format('F') . ' ' . $message->getSentDate()->format('m') . ' ' . $message->getSentHour();
                    $result .= '<br><div class = "date-separator" style="color: white;">'. $messageDate .'<hr></div>';
                }
                $id = ($message->getSender() == $self) ? "user_me" : "user_other";
                $result .= '<div class = "msg" id = "'.$id.'">
                                <p class = "msg-text">'.$message->getContent().'</p>
                            </div>';
                $previousSentDate = $message->getSentDate();
                $previousSentHour = $message->getSentHour();
            }
        }
        return $result;
    }

    function getTimeDifferenceInHours(DateTime $firstSentDate, string $firstSentHour,
                                      DateTime $secondSentDate, string $secondSentHour): int {
        $firstSentDateTime = DateTime::createFromFormat('Y-m-d H:i:s', $firstSentDate->format('Y-m-d') . ' ' . $firstSentHour);
        $secondSentDateTime = DateTime::createFromFormat('Y-m-d H:i:s', $secondSentDate->format('Y-m-d') . ' ' . $secondSentHour);
        if ($firstSentDateTime === false || $secondSentDateTime === false) {
            return 0;
        }
    
        $interval = $secondSentDateTime->diff($firstSentDateTime);
        
        return $interval->i + $interval->h * 60 + $interval->days * 24 * 60;
    }

?>