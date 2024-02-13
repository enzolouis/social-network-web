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
     * @param  PDO $pdo         PDO database connection
     * @param  string $login    User's login
     * @param  string $password User's password
     * @return bool
     */
    function isPassValid(PDO $pdo, string $login, string $password) : bool {
        $user = getUserByLogin($pdo, $login);
        if ($user && (password_verify($password, $user->getPassword()))) {
            return true;
        }
        return false;
    }

    /**
     * Start a session for the specified user login.
     *
     * @param PDO $pdo          PDO database connection
     * @param string $login     User's login
     * @return bool             Returns true if the session is started successfully; otherwise, returns false.
     */
    function startSessionForUser(PDO $pdo, string $login) : bool {
        $user = getUserByLogin($pdo, $login);
        if ($user) {
            $_SESSION['user'] = $user;
            return true;
        }
        return false;
    }

    /**
     * Check if the current session is valid by verifying its existence in the database.
     *
     * @return bool Returns the user for the current session.
     */
    function isSessionValid() : bool {
        session_start();
        if (isset($_SESSION['user']) && !empty($_SESSION['user'])) {
            $user = $_SESSION['user'];
            if ($user) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Returns the user asociated to the given login if found, null else.
     *
     * @param  PDO $pdo         PDO database connection
     * @param  string $login    User's login
     * @return User
     */
    function getUserByLogin(PDO $pdo, string $login) : User | null {
        $stmt = prepare($pdo, "SELECT * FROM user WHERE login = ?");
        execute($stmt, [$login]);

        if ($user = $stmt->fetch()) {
            return new User($user["login"], $user["username"], $user["password"], $user["description"], $user["profilePicture"]);
        }
        return null;
    }

    /**
     * Returns true if a user associated to the given login exists, false else
     *
     * @param PDO $pdo          PDO database connection
     * @param string $login     user's login
     * @return boolean
     */
    function isUserExist(PDO $pdo, string $login) : bool {
        $stmt = prepare($pdo, "SELECT * FROM user WHERE login = ?");
        execute($stmt, [$login]);

        return $stmt->fetch() ? true : false;
    }
    
    /**
     * Adds a user to the database
     *
     * @param  PDO $pdo         PDO database connection
     * @param  string $login    User's login
     * @param  string $username User's username
     * @param  string $password User's password
     */
    function addUser(PDO $pdo, string $login, string $username, string $password) {
        $stmt = prepare($pdo, "INSERT INTO user (login, username, password) VALUES (?, ?, ?)");
        $password = password_hash($password, PASSWORD_DEFAULT);
        execute($stmt, [$login, $username, $password]);
    }

    /**
     * Returns all contacted users by the user associated to the given login
     *
     * @param PDO $pdo
     * @param string $login
     * @return void
     */
    function getContactedUsers(PDO $pdo, string $login) : array | null {
        $stmt = prepare($pdo,  "SELECT DISTINCT user.* 
                                FROM user, message 
                                WHERE ((message.receiver = :login AND message.sender = user.login) 
                                        OR (message.sender = :login AND message.receiver = user.login)) 
                                AND user.login <> :login
                                ORDER BY (
                                    SELECT MAX(CONCAT(message.sentDate, ' ', message.sentHour))
                                    FROM message
                                    WHERE message.sender = user.login OR message.receiver = user.login
                                ) DESC");
        execute($stmt, [":login" => $login]);
        
        $users = array();
        while ($contactedUser = $stmt->fetch()) {
            $users[] = new User($contactedUser["login"], $contactedUser["username"], "Nothing to see here :)", $contactedUser["description"], $contactedUser["profilePicture"]);
        }
        return count($users) > 0 ? $users : null;
    }

    /**
     * Returns all users whom login are close to the researched login
     *
     * @param PDO $pdo
     * @param string $search
     * @return void
     */
    function findSearchedUsers(PDO $pdo, string $currentUser, string $search) : array | null {
        $stmt = prepare($pdo,  "SELECT *
                                FROM user
                                WHERE login <> :currentUser
                                AND (LEVENSHTEIN(login, :search) < 3 
                                OR login LIKE CONCAT('%', :search, '%'))
                                ORDER BY CHAR_LENGTH(login) - CHAR_LENGTH(:search),
                                         LEVENSHTEIN(login, :search)
                                LIMIT 10");
        execute($stmt, [":search" => $search, ":currentUser" => $currentUser]);

        $users = array();
        while ($contactedUser = $stmt->fetch()) {
            $users[] = new User($contactedUser["login"], $contactedUser["username"], "Nothing to see here :)", $contactedUser["description"], $contactedUser["profilePicture"]);
        }
        return count($users) > 0 ? $users : null;
    }
