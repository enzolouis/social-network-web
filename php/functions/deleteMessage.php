<?php
    session_start();
    require("databaseFunctions.php");
    require("messageDAO.php");
    $pdo = createConnection();
    deleteMessage($pdo, $_POST["id"]);
