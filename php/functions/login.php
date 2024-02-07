<?php
session_start();
require("databaseFunctions.php");
require("userDAO.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $login = $_POST["login"];
    $password = $_POST["password"];

    $pdo = createConnection();

    if (isPassValid($pdo, $login, $password)) {
        if (startSessionForUser($pdo, $login)) {
            header("Location: ../pages/chat.php");
            exit();
        }
    } else {
        header("Location: ../../index.php?passlogin=false");
        exit();
    }
}