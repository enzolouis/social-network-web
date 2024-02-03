<?php

require("functions.php");
require("userDAO.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $login = $_POST["login"];
    $username = $_POST["username"];
    $password = $_POST["password"];
    $passwordverify = $_POST["password-verify"];

    $pdo = createConnection();

    $user = getUserById($pdo, $login);

    if (!$user) {
        if ($password !== $passwordverify) {
            header("Location: ../html/register.html");
            exit();
        }

        if (strlen($password) < 8 || !preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
            header("Location: ../html/register.html");
            exit();
        }

        add($pdo, $login, $username, $password);
    }

    header("Location: ../html/register.html");
}