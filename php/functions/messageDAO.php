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
            return $this->_sentDate->format('F') . ' ' . $this->_sentDate->format('d') . ' - ' . $this->_sentHour . ' | ' . $this->_content . "<br>";
        }
        
    }

    function addMessage(PDO $pdo,
                        string $sender, 
                        string $receiver, 
                        string $sentDate,
                        string $sentHour,
                        string $content,
                        bool $liked) : int | null {
        $stmt = prepare($pdo, "INSERT INTO message (sender, receiver, sentDate, sentHour, content, liked) 
                                            VALUES (?, ?, ?, ?, ?, ?)");
        if (execute($stmt, [$sender, $receiver, $sentDate, $sentHour, $content, $liked])) {
            return $pdo->lastInsertId();
        }
        return null;
    }

    function deleteMessage(PDO $pdo, int $id) {
        $stmt = prepare($pdo, "DELETE FROM message WHERE id = ?");
        return execute($stmt, [$id]);
    }
    
    function editMessage(PDO $pdo, int $id, string $content) : bool {
        $stmt = prepare($pdo, "UPDATE message
                               SET content = ? 
                               WHERE id = ?");
        return execute($stmt, [$content, $id]);
    }

    function getMessagesBetweenPeople(PDO $pdo, string $personOne, string $personTwo) : array | null {
        $stmt = prepare($pdo, "SELECT * FROM message WHERE sender IN (:personOne, :personTwo) AND receiver IN (:personOne, :personTwo) ORDER BY sentDate ASC, sentHour ASC, id ASC");
        execute($stmt, [":personOne" => $personOne, ":personTwo" => $personTwo]);

        $messages = array();
        while ($message = $stmt->fetch()) {
            $messages[] = new Message($message["id"], $message["sender"], $message["receiver"], new DateTime($message["sentDate"]), $message["sentHour"], $message["content"], $message["liked"]);
        }
        return count($messages) > 0 ? $messages : null;
    }

    function getLastMessage(PDO $pdo, string $personOne, string $personTwo) : Message | null {
        $stmt = prepare($pdo, "SELECT * FROM message WHERE sender IN (:personOne, :personTwo) AND receiver IN (:personOne, :personTwo) ORDER BY sentDate DESC, sentHour DESC, id DESC LIMIT 1");
        execute($stmt, [":personOne" => $personOne, ":personTwo" => $personTwo]);

        if ($message = $stmt->fetch()) {
            return new Message($message["id"], $message["sender"], $message["receiver"], new DateTime($message["sentDate"]), $message["sentHour"], $message["content"], $message["liked"]);
        }
        return null;
    }
  
    function formatDate(DateTime $date, string $hour) : string {
        return $date->format('F') . ' ' . $date->format('d') . ' - ' . formatHour($hour);
    }

    function formatHour(string $hour) : string {
        return substr($hour, 0, 5);
    }