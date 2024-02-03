<?php

require("functions.php");
require("userDAO.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $login = $_POST["login"];
    $password = $_POST["password"];

    $pdo = createConnection();

    if (isPassValid($pdo, $login, $password)) {
        if(startSessionForUser($pdo, $login)) {
            header("Location: chat.php");
            exit();
        }
    } else {
        header("Location: ../index.html");
        exit();
    }
}