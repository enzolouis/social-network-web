<!DOCTYPE html>
<html>
<head>
    <title>Login Page</title>
    <link rel="stylesheet" type="login/css/login.css" href="styles.css">
</head>
<body>
    <?php 
    require("functions.php");
    require("userDAO.php");
    require("messageDAO.php");
    $pdo = createConnection();

    echo getUserById($pdo, "firstUser");
    foreach (getMessagesBetweenPeople($pdo, "xouxou", "Nautilus") as $message){
        if ($message->getSender() == "xouxou"){
            echo "<i>" . $message->getSentHour() . "</i><br>";
            echo $message->getContent() . "<br><br>";
        } else {
            echo str_repeat('&nbsp;', 50) . "<i>" . $message->getSentHour() . "</i><br>";
            echo str_repeat('&nbsp;', 50) . $message->getContent() . "<br><br>";
        }
        
    } 

    ?>
</body>
</html>