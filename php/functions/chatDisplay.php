<?php
    require("messageDAO.php");
    require("userDAO.php");

    // 
    function showDiscussionsChats(PDO $pdo, string $user) : string {
        $users = getContactedUsers($pdo, $user);
        $result = "";

        if ($users) {
            foreach ($users as $contactedUser) {
                $imageURL = empty($contactedUser->getProfilePicture()) ? '' : 'src = "'. $contactedUser->getProfilePicture() .'"';
                $messages = getMessagesBetweenPeople($pdo, $user, $contactedUser->getLogin());
                $lastMessage = end($messages);
                $who = ($lastMessage->getSender() == $user) ? "You: " : "Them: ";

                $result .= '<div class = "contacted-user" id = "'. $contactedUser->getLogin() .'" onclick="loadHeaderAndChat(\'' .$user. '\', this.id);">
                                <div class = "contacted-user-pfp-container">
                                    <img class = "contacted-user-pfp" '. $imageURL .'>
                                    <div class = "contacted-user-status"></div>
                                </div>
                                <div class = "contacted-user-infos">
                                    <p class = "contacted-user-name">'.$contactedUser->getUsername().'</p>
                                    <p class = "contacted-user-message">'.$who.$lastMessage->getContent().'</p>
                                </div>
                            </div>';
            }
        }
        return $result;
    }

    // 
    function showChatHeader(PDO $pdo, string $other) : string {
        $user = getUserByLogin($pdo, $other);
        $imageURL = empty($user->getProfilePicture()) ? '' : 'src = "'. $user->getProfilePicture() .'"';
        return '<div class = "contacted-user-pfp-container">
                    <img class = "contacted-user-pfp contacted-user-pfp-header" '. $imageURL .'>
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
                    $messageDate = $message->getSentDate()->format('F') . ' ' . $message->getSentDate()->format('m') . ' - ' . substr($message->getSentHour(), 0, 5);
                    $result .= '<div class = "date-separator">'. $messageDate .'</div>';
                }
                $id = ($message->getSender() == $self) ? "user_me" : "user_other";
                $result .= addChatMessage($id, $message->getId(), $message->getContent());
                $previousSentDate = $message->getSentDate();
                $previousSentHour = $message->getSentHour();
            }
        }
        return $result;
    }

    function addChatMessage(string $idUser, string $idMessage, string $content): string {

        // If the message comes from the user, we can edit it and therefore we have the options available
        $editable = $idUser == "user_me" ? "<div class='msg-option-separator'></div>
                                            <i class='msg-option fa-solid fa-pen-to-square' onClick='editMessage(this.parentNode.parentNode.id)'></i>
                                            <div class='msg-option-separator'></div>
                                            <i class='msg-option fa-solid fa-trash' onClick='deleteMessage(this.parentNode.parentNode.id)'></i>" 
                                            : '';

        // The message div
        $result =  '<div class = "msg '.$idUser.'" id = "'.$idMessage.'">
                        <div class = "msg-options">
                            <i class="msg-option fa-solid fa-share"></i>
                            <div class="msg-option-separator"></div>
                            <i class="msg-option fa-solid fa-copy" onClick="copyMessage(this.parentNode.parentNode.id)"></i>
                            '.$editable.'
                        </div>
                        <p class = "msg-text">'.$content.'</p>
                    </div>';

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