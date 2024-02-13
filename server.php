<?php require __DIR__.'/vendor/autoload.php';

// require("php/functions/userDAO.php");

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

define('APP_PORT', 8080);

class ServerImpl implements MessageComponentInterface {
    protected $clients;
    protected $sessions;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
        $this->sessions = [];
        echo "Initialized !\n";
    }

    public function onOpen(ConnectionInterface $conn) {
        parse_str($conn->httpRequest->getUri()->getQuery(), $queryParameters);
        $conn->login = sprintf("%s", $queryParameters['from']);
        $conn->to = sprintf("%s", $queryParameters['to']);
        $this->clients->attach($conn);
        echo "New connection! ({$conn->resourceId}).\n";
    }

    public function onMessage(ConnectionInterface $conn, $msg) {
        echo sprintf("New message from '%s': %s\n\n\n", $conn->resourceId, $msg);
        
        foreach ($this->clients as $client) { // BROADCAST
            echo "Conversation ciblée : de " . $client->login . " à " .$client->to. "\n";
            if ($conn == $client) {
                echo "Conversation stoppé : la cible est pareil que l'envoyeur\n\n";
                continue;
            }
            echo "Conversation conservé : la cible est différente de l'envoyeur\n";
            if ($client->to != $conn->to) {
                echo "Conversation stoppé : la cible n'est pas la cible spécifié\n\n";
                continue;
            }
            echo "Succès! Conversation conservé : la cible est la cible!";
            //echo $client->to . "\n";
            //echo $conn->to . "\n";
            $client->send($msg);
        }
        
    }

    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
        echo "Connection {$conn->resourceId} is gone.\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error occured on connection {$conn->resourceId}: {$e->getMessage()}\n\n\n";
        $conn->close();
    }
}

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new ServerImpl()
        )
    ),
    APP_PORT,
);

echo "Server created on port " . APP_PORT . "\n\n";
$server->run();