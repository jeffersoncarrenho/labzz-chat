<?php

require __DIR__.'/vendor/autoload.php';

use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use Predis\Client;
use App\WebSocket\ChatServer;

$chatServer = new ChatServer();

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new ChatServer()
        )
    ),
    8081
);

echo "WebSocket server running on port 8081\n";

// escutar o Redis para novas mensagens e broadcast para clientes WebSocket
$redis = new Client();
$pubsub = $redis->pubSubLoop();
$pubsub->subscribe('chat');

foreach ($pubsub as $message) {
    if ($message->kind === 'message') {
        $data = json_decode($message->payload, true);
        $conversationId = $data['conversation_id'];
        
        $chatServer->broadcastToRoom(
            $conversationId,
            json_encode($data)
        );
            
            echo "Nova mensagem recebida: {$message->payload}\n";
        }
}

$server->run();