<?php
    require("databaseFunctions.php");
    require("chatDisplay.php");
    session_start();

    if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST["search"])) {
        $currentUser = $_SESSION["user"]->getLogin();
        $search = $_POST["search"];
        echo getFoundUsers(createConnection(), $currentUser, $search);
    }