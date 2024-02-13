<?php
    session_start();
    require("databaseFunctions.php");
    require("chatDisplay.php");

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (!empty($_POST["currentUser"])) { 
            $pdo = createConnection();
            echo showDiscussionsChats($pdo, $_POST["currentUser"]);
        } 
    }