<?php
session_start();
require("databaseFunctions.php");
require("userDAO.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $login = $_POST["login"];
    $username = $_POST["username"];
    $password = $_POST["password"];
    $passwordverify = $_POST["password-verify"];

    $pdo = createConnection();

    $user = getUserByLogin($pdo, $login);

    if (!$user) {
        if ($password !== $passwordverify) {
            header("Location: ../pages/register.html");
            exit();
        }

        if (preg_match('/[ ]/', $login) || preg_match('/[ ]/', $password)) {
            header("Location: ../pages/register.html");
            exit();
        }

        if (
            strlen($password) < 8 ||
            !preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password) ||
            !preg_match('/[A-Z]/', $password) ||
            !preg_match('/[0-9]/', $password)
        ) {
            header("Location: ../pages/register.html");
            exit();
        }

        addUser($pdo, $login, $username, $password);
    }

    header("Location: ../pages/register.html");
    exit();
}