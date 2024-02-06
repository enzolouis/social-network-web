<?php
$showCreated = isset($_GET['created']) ? $_GET['created'] : false;
$wrongPass = isset($_GET['passlogin']) ? $_GET['passlogin'] : null;
?>

<!DOCTYPE html>
<head>
    <link rel="stylesheet" type="text/css" href="css/reset.css">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" type="text/css" href="css/login.css">
</head>
<html>
    <body>
        <div class="account-created" <?php if(!$showCreated) { echo 'style="display: none;"'; }?> >
            <i></i>
            <p>Your account has been created.</p>
        </div>
        <form method="post" action="php/functions/login.php"  class="form-container form-login">
            <label for="login">Login</label>
            <input type="text" class="login" id="login" name="login" placeholder="edgeur12" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="NX:b7!LrPg@t2X9" required>
            <label class="secure-password-and-bad-password" <?php if($wrongPass === null) { echo 'style="display: none;"'; }?> >Incorrect login or password.</label>

            <input type="submit" value="Login">

            <div class="separator"></div>
            <p class="already-account">Don't have an account? <a href="php/pages/register.html">Register now</a></p>
        </form>
        <script src="js/login.js"></script>
    </body>
</html>