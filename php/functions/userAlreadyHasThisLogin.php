<?php

require('databaseFunctions.php');
require('userDAO.php');

$pdo = createConnection();

$loginReceived = $_GET['login'];

echo isUserExist($pdo, $loginReceived);