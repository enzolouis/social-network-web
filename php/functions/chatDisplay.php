<?php
    require("messageDAO.php");
    require("userDAO.php");


    /**
     * Gives all the available chats of the current user as a formatted HTML string
     * Uses userDAO/getContactedUsers()
     *      userDAO/getProfilePicture() 
     *      userDAO/getLogin() 
     *      userDAO/getUsername() 
     *      messageDAO/getContent() 
     *      messageDAO/getSender() 
     *      messageDAO/getMessagesBetweenPeople() 
     *
     * @param  PDO    $pdo      PDO database connection
     * @param  string $user     User's login
     * @return string
     */
    function getDiscussions(PDO $pdo, string $user) : string {
        $users = getContactedUsers($pdo, $user);
        $result = "";

        // If there is a least one contacted user 
        // for each of them make formatted HTML string containing their name and the last message exchanged
        if ($users) {
            foreach ($users as $contactedUser) {

                // Gets the contacted user's pfp
                $imageURL = empty($contactedUser->getProfilePicture()) ? '' : 'src = "'. $contactedUser->getProfilePicture() .'"';

                // Gets all the messages to take the last one and write 'You: ' or 'Them: ' before depending on who texted
                $messages = getMessagesBetweenPeople($pdo, $user, $contactedUser->getLogin());
                $lastMessage = end($messages);
                $who = ($lastMessage->getSender() == $user) ? "You: " : "Them: ";

                // Makes the formatted HTML string for the user
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

        // Returns the formatted HTML string
        return $result;
    }


    /**
     * Shows the current chat header (so like the other user's name, pfp and bio) as a formatted HTML string
     * Uses userDAO/getUserByLogin()
     *      userDAO/getProfilePicture() 
     *      userDAO/getUsername() 
     *      userDAO/getDescription() 
     * 
     * @param  PDO    $pdo      PDO database connection
     * @param  string $other    Other user's login
     * @return string
     */
    function getChatHeader(PDO $pdo, string $other) : string {

        // Gets the other user's pfp
        $user = getUserByLogin($pdo, $other);
        $imageURL = empty($user->getProfilePicture()) ? '' : 'src = "'. $user->getProfilePicture() .'"';

        // Returns the formatted HTML string
        return '<div class = "contacted-user-pfp-container">
                    <img class = "contacted-user-pfp contacted-user-pfp-header" '. $imageURL .'>
                    <div class = "contacted-user-status"></div>
                </div>
                <div class = "contacted-user-infos">
                    <p class = "contacted-user-name">'.$user->getUsername().'</p>
                    <p class = "contacted-user-description">'.$user->getDescription().'</p>
                </div>';
    }


    /**
     * Gives all the messages exchanged between two users as a formatted HTML string
     * Uses messageDAO/getMessagesBetweenPeople() 
     *      messageDAO/getSentDate() 
     *      messageDAO/getSentHour() 
     *      messageDAO/getSender() 
     *      messageDAO/getId() 
     *      messageDAO/getContent() 
     *      chatDisplay/getTimeDifferenceInHours()
     *      chatDisplay/addChatMessage()
     * 
     * @param  PDO    $pdo      PDO database connection
     * @param  string $self     Current user's login
     * @param  string $other    Other user's login
     * @return string
     */
    function getChatMessages(PDO $pdo, string $self, string $other) : string {

        // Gets all the messages exchanged between the two users as an array
        $messages = getMessagesBetweenPeople($pdo, $self, $other);
        $result = "";
        
        // If there is at least a message
        // For each one of them it will check its date and hour (to show a separator or no)
        // And check who's the sender and then make the correct HTML string for it (using addChatMessage)
        if ($messages) {

            // Top chat date separator
            $previousSentDate = $messages[0]->getSentDate();
            $previousSentHour = $messages[0]->getSentHour();
            $messageDate = $messages[0]->getSentDate()->format('F') . ' ' . $messages[0]->getSentDate()->format('d') . ' - ' . substr($messages[0]->getSentHour(), 0, 5);
            $result .= '<div class = "date-separator">'. $messageDate .'</div>';

            foreach ($messages as $message) {

                // Checks whether the message comes from the logged user or the other user
                $id = ($message->getSender() == $self) ? "user_me" : "user_other";

                // Checks if a date separator is needed and if yes adds it to the string
                if (!($message->getSentDate() == $previousSentDate)) {
                    $messageDate = $message->getSentDate()->format('F') . ' ' . $message->getSentDate()->format('d') . ' - ' . substr($message->getSentHour(), 0, 5);
                    $result .= '<div class = "date-separator">'. $messageDate .'</div>';
                } else if (getTimeDifferenceInHours($message->getSentDate(), $message->getSentHour(), $previousSentDate, $previousSentHour) > 300) {
                    $messageHour = substr($message->getSentHour(), 0, 5);
                    $result .= '<div class = "'. $id .' hour-separator">'. $messageHour .'</div>';
                }

                // Makes the current message HTML string
                $result .= addChatMessage($id, $message->getId(), $message->getContent());

                // Updates checking hours
                $previousSentDate = $message->getSentDate();
                $previousSentHour = $message->getSentHour();
            }
        }

        // Returns the formatted string
        return $result;
    }


    /**
     * Gives a formatted HTML string for a specific chat message
     * 
     * @param  string $idUser       HTML Id showing who sent the message (current user or other), used in the CSS later
     * @param  string $idMessage    The database id of the message  
     * @param  string $content      The content of the message 
     * @return string
     */
    function addChatMessage(string $idUser, string $idMessage, string $content): string {

        // If the message comes from the user, we can edit it and therefore we have the options available
        $editable = $idUser == "user_me" ? "<div class='msg-option-separator'></div>
                                            <i class='msg-option fa-solid fa-pen-to-square' onClick='editMessage(this.parentNode.parentNode.id)'></i>
                                            <div class='msg-option-separator'></div>
                                            <i class='msg-option fa-solid fa-trash' onClick='deleteMessage(this.parentNode.parentNode.id)'></i>" 
        /* Else we can't so no options */   : '';

        // The message div
        return '<div class = "msg '.$idUser.'" id = "'.$idMessage.'">
                    <div class = "msg-options">
                        <i class="msg-option fa-solid fa-share"></i>
                        <div class="msg-option-separator"></div>
                        <i class="msg-option fa-solid fa-copy" onClick="copyMessage(this.parentNode.parentNode.id)"></i>
                        '.$editable.'
                    </div>
                    <p class = "msg-text">'.$content.'</p>
                </div>';
    }


    /**
     * Gives all the messages exchanged between two users as a formatted HTML string
     * 
     * @param  DateTime $firstSentDate  The date of the first message
     * @param  string   $firstSentHour  The hour of the first message
     * @param  DateTime $secondSentDate The date of the second message
     * @param  string   $secondSentHour The hour of the second message
     * @return int
     */
    function getTimeDifferenceInHours(DateTime $firstSentDate, string $firstSentHour,
                                      DateTime $secondSentDate, string $secondSentHour): int {

        // Formats the dates
        $firstSentDateTime = DateTime::createFromFormat('Y-m-d H:i:s', $firstSentDate->format('Y-m-d') . ' ' . $firstSentHour);
        $secondSentDateTime = DateTime::createFromFormat('Y-m-d H:i:s', $secondSentDate->format('Y-m-d') . ' ' . $secondSentHour);
        if ($firstSentDateTime === false || $secondSentDateTime === false) return 0;
    
        // Checks the difference and returns it
        $interval = $secondSentDateTime->diff($firstSentDateTime);
        return $interval->i + $interval->h * 60 + $interval->days * 24 * 60;
    }


    function getFoundUsers(PDO $pdo, string $currentUser, string $search) : string {
        $users = findSearchedUsers($pdo, $currentUser, $search); 
        $result = "";

        if ($users) {
            foreach ($users as $foundUser) {
                $imageURL = empty($foundUser->getProfilePicture()) ? '' : 'src = "'. $foundUser->getProfilePicture() .'"';
                $result .= '<div class = "contacted-user" id = "'. $foundUser->getLogin() .'" onclick="hideFoundUsers(); loadHeaderAndChat(\'' .$currentUser. '\', this.id);">
                                <div class = "contacted-user-pfp-container">
                                    <img class = "contacted-user-pfp" '. $imageURL .'>
                                    <div class = "contacted-user-status"></div>
                                </div>
                                <div class = "contacted-user-infos">
                                    <p class = "contacted-user-name">'.$foundUser->getUsername().'</p>
                                    <p class = "contacted-user-description">'.$foundUser->getDescription().'</p>
                                </div>
                            </div>';
            }
        }
        return $result;
    }