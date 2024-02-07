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
    echo "yo";
    echo getUserByLogin($pdo, "firstUser");
    $xouxou = new User('xouxou', 'Maxence Maury-Balit', 'xxx', 'Wise mystical tree enjoyer');
    $nautilus = new User('Nautilus', 'Zoubairov Ibrahim', 'yyy', 'NOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOON');
    foreach (getMessagesBetweenPeople($pdo, $xouxou, $nautilus) as $message){
        if ($message->getSender() == "xouxou"){
            echo "<i>" . $message->getSentHour() . "</i><br>";
            echo $message->getContent() . "<br><br>";
        } else {
            echo str_repeat('&nbsp;', 50) . "<i>" . $message->getSentHour() . "</i><br>";
            echo str_repeat('&nbsp;', 50) . $message->getContent() . "<br><br>";
        }
    }
 
    addMessage($pdo, $xouxou, $nautilus, date('Y-m-d'), "17:59:42", "Bye bye", 0);

    echo "<br><br><br><br>";
    foreach (getMessagesBetweenPeople($pdo, $xouxou, $nautilus) as $message){
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