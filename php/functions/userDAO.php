<?php

    class User {

        private $_login;
        private $_username;
        private $_password;
        private $_description;
        private $_profilePicture;
        
        function __construct($login, $username, $password, $description, $profilePicture = null){
            $this->_login = $login;
            $this->_username = $username;
            $this->_password = $password;
            $this->_description = $description;
            $this->_profilePicture = $profilePicture;
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

        public function getProfilePicture(){
            return $this->_profilePicture;
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
     * @return User Returns the user for the current session.
     */
    function isSessionValid(): bool {
        session_start();

        if (isset($_SESSION['user'])) {

            $user = $_SESSION['user'];

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
            return new User($user["login"], $user["username"], $user["password"], $user["description"], $user["profilePicture"]);
        }
        return null;
    }

    function isUserExist(PDO $pdo, string $id) : bool {
        $stmt = prepare($pdo, "SELECT * FROM user WHERE login = ?");
        execute($stmt, [$id]);

        return $stmt->fetch() ? true : false;
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
        $stmt = prepare($pdo, "INSERT INTO user (login, username, password) VALUES (?, ?, ?, ?, ?)");
        $password = password_hash($password, PASSWORD_DEFAULT);
        execute($stmt, [$login, $username, $password]);
        header("Location: ../index.php?created=true");
        exit();
    }

    function getContactedUsers(PDO $pdo, string $user) : array | null {
        $stmt = prepare($pdo, "SELECT DISTINCT user.* FROM user, message WHERE ((message.receiver = :login AND message.sender = user.login) OR (message.sender = :login AND message.receiver = user.login)) AND user.login <> :login");
        execute($stmt, [":login" => $user]);
        
        $users = array();
        while ($contactedUser = $stmt->fetch()) {
            $users[] = new User($contactedUser["login"], $contactedUser["username"], "Nothing to see here :)", $contactedUser["description"], $contactedUser["profilePicture"]);
        }
        return count($users) > 0 ? $users : null;
    }
