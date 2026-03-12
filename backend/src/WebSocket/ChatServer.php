<?php

namespace App\WebSocket;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class ChatServer implements MessageComponentInterface {

    protected array $clients = [];
    protected array $rooms = [];

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients[$conn->resourceId] = $conn;

        echo "Nova conexão: {$conn->resourceId}\n";
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $data = json_decode($msg, true);

        /*
        cliente envia:
        { "action":"join", "conversation_id":1 }
        */

        if ($data['action'] === 'join') {

            $conversationId = $data['conversation_id'];

            if (!isset($this->rooms[$conversationId])) {
                $this->rooms[$conversationId] = [];
            }

            $this->rooms[$conversationId][$from->resourceId] = $from;

            echo "Cliente {$from->resourceId} entrou na conversa {$conversationId}\n";
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        unset($this->clients[$conn->resourceId]);

        foreach ($this->rooms as $conversationId => $clients) {
            unset($this->rooms[$conversationId][$conn->resourceId]);
        }

        echo "Conexão {$conn->resourceId} fechada\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "Erro: {$e->getMessage()}\n";

        $conn->close();
    }

    public function broadcastToRoom($conversationId, $message)
    {
        if (!isset($this->rooms[$conversationId])) {
            return;
        }

        foreach ($this->rooms[$conversationId] as $client) {
            $client->send($message);
        }
    }
}