<?php

    class User {

        private $_login;
        private $_username;
        private $_password;
        private $_description;
        
        function __construct($login, $username, $password, $description){
            $this->_login = $login;
            $this->_username = $username;
            $this->_password = $password;
            $this->_description = $description;
        }

        public function getLogin(){
            return $this->_login;
        }

        public function getUsername(){
            return $this->_username;
        }

        public function getPassword(){
            return $this->_password;
        }

        public function getDescription(){
            return $this->_description;
        }

        public function __toString(){
            return $this->_username . "<br>";
        }

    }

    function getUserById(PDO $pdo, string $id) : User | null {
        $stmt = prepare($pdo, "SELECT * FROM user WHERE login = ?");
        execute($stmt, [$id]);
        if ($user = $stmt->fetch()) {
            return new User($user["login"], $user["username"], $user["password"], $user["description"]);
        }
        return null;
    }
    
?>