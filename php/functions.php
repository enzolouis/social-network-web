<?php
    
    function createConnection() : PDO {
        $pdo = null;
        try {
            $pdo = new PDO('mysql:host=localhost;dbname=socialnetwork;charset=utf8', 'root', '');
        } catch (Exception $e) {
            echo ("Failed to load database");
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

    function execute($stmt, $arguments = array()) {
        $stmt->execute($arguments);
        if (!$stmt) {
            echo "Execute error : " . $stmt->errorInfo(); exit(1); 
        }
    }

?>