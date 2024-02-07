<?php
    session_start();
    require("messageDAO.php");
    require("databaseFunctions.php");
    $pdo = createConnection();

    $id = $_POST['id'];
    $msg = $_POST['text'];

    echo updateMessageText($pdo, $id, $msg);
    