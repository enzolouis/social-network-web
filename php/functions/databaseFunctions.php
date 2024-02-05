<?php
    
    function createConnection() : PDO {
        $pdo = null;
        try {
            $pdo = new PDO('mysql:host=sql11.freemysqlhosting.net;dbname=sql11681796;charset=utf8', 'sql11681796', 'cntZXW4Tx8');
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