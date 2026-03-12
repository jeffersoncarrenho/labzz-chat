<?php

namespace App\WebSocket;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class ChatServer implements MessageComponentInterface {

    protected array $clients = [];
    protected array $rooms = [];
    protected array $users = [];

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients[$conn->resourceId] = $conn;

        echo "Nova conexão {$conn->resourceId}\n";
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $data = json_decode($msg, true);

        if (!$data) {
            return;
        }

        /*
        AUTENTICAÇÃO
        */

        if ($data['action'] === 'auth') {

            try {

                $decoded = JWT::decode(
                    $data['token'],
                    new Key($_ENV['JWT_SECRET'], 'HS256')
                );

                $this->users[$from->resourceId] = $decoded->user_id;

                echo "Usuário {$decoded->user_id} autenticado\n";

            } catch (\Exception $e) {

                echo "JWT inválido\n";

                $from->close();
            }

            return;
        }

        /*
        JOIN ROOM
        */

        if ($data['action'] === 'join') {

            if (!isset($this->users[$from->resourceId])) {
                echo "Cliente não autenticado\n";
                return;
            }

            $conversationId = $data['conversation_id'];

            if (!isset($this->rooms[$conversationId])) {
                $this->rooms[$conversationId] = [];
            }

            $this->rooms[$conversationId][$from->resourceId] = $from;

            echo "Usuário {$this->users[$from->resourceId]} entrou na conversa {$conversationId}\n";
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        unset($this->clients[$conn->resourceId]);
        unset($this->users[$conn->resourceId]);

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