<?php
    require("databaseFunctions.php");
    require("chatDisplay.php");
    $pdo = createConnection();

    echo showHeader($pdo, $_POST["otherUser"]);