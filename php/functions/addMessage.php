<?php
    session_start();
    require("databaseFunctions.php");
    require("messageDAO.php");

    $pdo = createConnection();
    echo addMessage($pdo, $_POST["user"], $_POST["otherUser"], date("Y-m-d"), date("H:i:s"), $_POST["content"], false);