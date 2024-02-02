<?php

    class Message {

        private int $_id;
        private string $_sender;
        private string $_receiver;
        private DateTime $_sentDate;
        private string $_sentHour;
        private string $_content;
        private bool $_liked;
        
        function __construct(int $id,
                             string $sender, 
                             string $receiver, 
                             DateTime $sentDate,
                             string $sentHour,
                             string $content,
                             bool $liked){
            $this->_id = $id;
            $this->_sender = $sender;
            $this->_receiver = $receiver;
            $this->_sentDate = $sentDate;
            $this->_sentHour = $sentHour;
            $this->_content = $content;
            $this->_liked = $liked;
        }

        public function getId() : int {
            return $this->_id;
        }

        public function getSender() : string {
            return $this->_sender;
        }

        public function getReceiver() : string {
            return $this->_receiver;
        }

        public function getSentDate() : DateTime {
            return $this->_sentDate;
        }

        public function getSentHour() : string {
            return $this->_sentHour;
        }

        public function getContent() : string {
            return $this->_content;
        }

        public function isLiked() : bool {
            return $this->_liked;
        }

        public function __toString(){
            return $this->_content . "<br>";
        }
        
    }

    function addMessage(PDO $pdo,
                        User $sender, 
                        User $receiver, 
                        string $sentDate,
                        string $sentHour,
                        string $content,
                        bool $liked) {
        $stmt = prepare($pdo, "INSERT INTO message (sender, receiver, sentDate, sentHour, content, liked) 
                                            VALUES (?, ?, ?, ?, ?, ?)");
        execute($stmt, [$sender->getLogin(), $receiver->getLogin(), $sentDate, $sentHour, $content, $liked]);
    }

    function getMessagesBetweenPeople(PDO $pdo, User $personOne, User $personTwo) : array | null {
        $stmt = prepare($pdo, "SELECT * FROM message WHERE sender IN (:personOne, :personTwo) AND receiver IN (:personOne, :personTwo) ORDER BY sentDate ASC, sentHour ASC, id ASC");
        execute($stmt, [":personOne" => $personOne->getLogin(), ":personTwo" => $personTwo->getLogin()]);

        $messages = array();
        while ($message = $stmt->fetch()) {
            $messages[] = new Message($message["id"], $message["sender"], $message["receiver"], new DateTime($message["sentDate"]), $message["sentHour"], $message["content"], $message["liked"]);
        }
        return count($messages) > 0 ? $messages : null;
    }
    
?>