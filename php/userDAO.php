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
    
    /**
     * Checks if the password entered by the user matches the hashed password stored in the database for the specified login.
     *
     * @param  PDO $pdo
     * @param  mixed $login
     * @param  mixed $password
     * @return bool
     */
    function isPassValid(PDO $pdo, $login, $password): bool {
        $user = getUserById($pdo, $login);
    
        if ($user && (password_verify($password, $user->getPassword()))) {
            return true;
        }
    
        return false;
    }

    /**
     * Start a session for the specified user login.
     *
     * @param PDO $pdo The PDO database connection.
     * @param mixed $login The login identifier for the user.
     * @return bool Returns true if the session is started successfully; otherwise, returns false.
     */
    function startSessionForUser(PDO $pdo, $login): bool {
        $user = getUserById($pdo, $login);

        if ($user) {
            session_start();
            $_SESSION['user'] = $user;
            return true;
        }

        return false;
    }

    /**
     * Check if the current session is valid by verifying its existence in the database.
     *
     * @param PDO $pdo The PDO database connection.
     * @return bool Returns true if the session is valid; otherwise, returns false.
     */
    function isSessionValid(PDO $pdo): bool {
        session_start();

        if (isset($_SESSION['login'])) {
            $login = $_SESSION['login'];

            $user = getUserById($pdo, $login);

            if ($user) {
                return true;
            }
        }

        return false;
    }


    
    /**
     * Returns the user asociated to the login $id if found, null else.
     *
     * @param  PDO $pdo The PDO database connection.
     * @param  string $id
     * @return User
     */
    function getUserById(PDO $pdo, string $id) : User | null {
        $stmt = prepare($pdo, "SELECT * FROM user WHERE login = ?");
        execute($stmt, [$id]);

        if ($user = $stmt->fetch()) {
            return new User($user["login"], $user["username"], $user["password"], $user["description"]);
        }

        return null;
    }
    
    /**
     * Adds a user to the database
     *
     * @param  PDO $pdo The PDO database connection.
     * @param  string $login
     * @param  string $username
     * @param  string $password
     */
    function addUser(PDO $pdo, string $login, string $username, string $password) {
        $stmt = prepare($pdo, "INSERT INTO `user` (`login`, `username`, `password`, `description`) VALUES (?, ?, ?, ?)");
        $password = password_hash($password, PASSWORD_DEFAULT);
        $desc = "";
        execute($stmt, [$login, $username, $password, $desc]);
        header("Location: ../index.html");
        exit();
    }

    function getContactedUsers(PDO $pdo, User $user) : array | null {
        $stmt = prepare($pdo, "SELECT DISTINCT user.* FROM user, message WHERE user.login = message.receiver AND message.sender = ?");
        execute($stmt, [$user->getLogin()]);
        
        $users = array();
        while ($contactedUser = $stmt->fetch()) {
            $users[] = new User($contactedUser["login"], $contactedUser["username"], "Nothing to see here :)", $contactedUser["description"]);
        }
        return count($users) > 0 ? $users : null;
    }
