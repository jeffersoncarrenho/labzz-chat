<?php

namespace App\Controllers;
use App\Core\Database;
use App\Services\SearchService;
use Elastic\Elasticsearch\ClientBuilder;
use Predis\Client;
use PDO;

class MessageController
{

    public function send()
    {

        $data = json_decode(file_get_contents("php://input"), true);

        if (
            !isset($data['conversation_id']) ||
            !isset($data['user_id']) ||
            !isset($data['message'])
        ) {

            http_response_code(400);

            echo json_encode([
                "error" => "invalid request"
            ]);

            return;
        }

        $conversation_id = $data['conversation_id'];
        $user_id = $data['user_id'];
        $message = $data['message'];

        $db = Database::connect();

        /*
        verificar se usuário pertence à conversa
        */

        $stmt = $db->prepare("
            SELECT id
            FROM conversation_participants
            WHERE conversation_id = ?
            AND user_id = ?
        ");

        $stmt->execute([
            $conversation_id,
            $user_id
        ]);

        $participant = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$participant) {

            http_response_code(403);

            echo json_encode([
                "error" => "user not in conversation"
            ]);

            return;
        }

        /*
        salvar mensagem
        */

        $stmt = $db->prepare("
            INSERT INTO messages
            (conversation_id, user_id, message)
            VALUES (?, ?, ?)
        ");

        $stmt->execute([
            $conversation_id,
            $user_id,
            $message
        ]);

        $message_id = $db->lastInsertId();

        /*
        INDEXAR NO ELASTICSEARCH
        */

        $search = new SearchService();
        $search->indexMessage([
            'conversation_id' => $conversation_id,
            'user_id' => $user_id,
            'message' => $message,
            'created_at' => date("c")
        ], $message_id);


        /*
            PUBLICAR NO REDIS
        */

        $redis = new Client();

        $redis->publish("chat", json_encode([
            "message_id"=>$message_id,
            "conversation_id"=>$conversation_id,
            "user_id"=>$user_id,
            "message"=>$message
        ]));

        echo json_encode([
            "status"=>"ok",
            "message_id"=>$message_id
        ]);

        echo json_encode([
            "status" => "ok",
            "message_id" => $message_id
        ]);
    }

    public function list()
    {
        $conversation_id = $_GET['conversation_id'] ?? null;
        $page = $_GET['page'] ?? 1;

        if (!$conversation_id) {
            http_response_code(400);
            echo json_encode(["error" => "conversation_id required"]);
            return;
        }

        $limit = 20;
        $offset = ($page - 1) * $limit;

        $db = Database::connect();

        $stmt = $db->prepare("
            SELECT id, conversation_id, user_id, message, created_at
            FROM messages
            WHERE conversation_id = ?
            ORDER BY created_at DESC
            LIMIT ? OFFSET ?
        ");

        $stmt->bindValue(1, $conversation_id, PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, PDO::PARAM_INT);
        $stmt->bindValue(3, $offset, PDO::PARAM_INT);

        $stmt->execute();

        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            "page" => (int)$page,
            "messages" => $messages
        ]);
    }
}