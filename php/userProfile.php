<?php

require("functions.php");
require("userDAO.php");

$user = null;

if(isSessionValid()) {
    $user = $_SESSION["user"];
} else {
    disconnect();
}

$pdo = createConnection();
?>

<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="../css/userProfile.css">
        <link rel="stylesheet" href="../css/style.css">
        <script defer src="https://use.fontawesome.com/releases/v6.4.2/js/all.js"></script>
    </head>
    <body>
        <?php require("header.html"); ?>
        <main>    
            <div class="under-header">
                <div class="title-container">
                    <h1>User settings</h1>
                </div>
                <div class="button-container">
                    <button class="settings-menu-button" id="my-action-button" onclick="changeButtonColor(this)">My account</button>
                    <button class="settings-menu-button" id="my-account">My account</button>
                    <button class="settings-menu-button" id="my-account">My account</button>
                    <button class="settings-menu-button" id="my-account">My account</button>
                </div>
            </div>
            
            <div class="container">
                <h1 class="title">Username</h1>
                <label type="text" class="title"><?php echo $user->getUsername(); ?></label>
            </div>
        </main>
    </body>
</html>
