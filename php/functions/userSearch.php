<?php
    session_start();
    require("databaseFunctions.php");
    require("userDAO.php");

    if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST["search"])) {
        echo json_encode(findSearchedUsers($_PDO, $_POST["search"]));
    }