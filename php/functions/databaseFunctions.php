<?php

    function createConnection() : PDO {
        $pdo = null;
        try {
            $pdo = new PDO('mysql:host=maxence-maury-balit.com;dbname=socialnetwork;port=3306;charset=utf8', 'remoteuser', 'ylapin');
        } catch (Exception $e) {
            echo ("Failed to load database : ". $e->getMessage());
            exit(1);
        }
        return $pdo;
    }

    function prepare($pdo, $query) : PDOStatement {
        $stmt = $pdo->prepare($query);
        if (!$stmt) { 
            echo "Prepare error : " . $stmt->errorInfo(); exit(1); 
        }
        return $stmt;
    }

    function execute($stmt, $arguments = array()) : bool {
        $stmt->execute($arguments);
        if (!$stmt) {
            echo "Execute error : " . $stmt->errorInfo(); return false; 
        }
        return true;
    }

    function disconnect() {
        session_unset();
        header("Location: ../../index.php");
        exit();
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST["disconnect"])) {
        session_start();
        disconnect();
    }
