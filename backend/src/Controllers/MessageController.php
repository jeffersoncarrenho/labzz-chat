<?php

namespace App\Controllers;

use App\Core\Database;
use App\Services\SearchService;
use Predis\Client;
use OpenApi\Attributes as OA;

class MessageController
{

    #[OA\Post(
        path: "/messages",
        tags: ["Messages"],
        summary: "Send a message to a conversation",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["conversation_id","user_id","message"],
                properties: [
                    new OA\Property(property: "conversation_id", type: "integer", example: 1),
                    new OA\Property(property: "user_id", type: "integer", example: 1),
                    new OA\Property(property: "message", type: "string", example: "Hello Labzz!")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Message sent"
            ),
            new OA\Response(
                response: 403,
                description: "User not part of conversation"
            )
        ]
    )]

    public function send()
    {

        $data = json_decode(file_get_contents("php://input"), true);

        $conversation_id = $data["conversation_id"];
        $user_id = $data["user_id"];
        $message = $data["message"];

        $db = Database::connect();

        /*
        VALIDAR PARTICIPAÇÃO
        */

        $stmt = $db->prepare(
            "SELECT id FROM conversation_participants 
             WHERE conversation_id = ? AND user_id = ?"
        );

        $stmt->execute([$conversation_id, $user_id]);

        if (!$stmt->fetch()) {
            http_response_code(403);
            echo json_encode(["error" => "User not in conversation"]);
            return;
        }

        /*
        SALVAR MENSAGEM
        */

        $stmt = $db->prepare(
            "INSERT INTO messages (conversation_id,user_id,message) 
             VALUES (?,?,?)"
        );

        $stmt->execute([$conversation_id, $user_id, $message]);

        $message_id = $db->lastInsertId();

        /*
        REDIS PUB/SUB
        */

        $redis = new Client([
            'host' => 'redis',
            'port' => 6379
        ]);

        $payload = [
            "message_id" => $message_id,
            "conversation_id" => $conversation_id,
            "user_id" => $user_id,
            "message" => $message
        ];

        $redis->publish("chat", json_encode($payload));

        /*
        ELASTICSEARCH INDEX
        */

        $search = new SearchService();

        $search->indexMessage([
            "conversation_id" => $conversation_id,
            "user_id" => $user_id,
            "message" => $message,
            "created_at" => date("c")
        ], $message_id);

        /*
        RESPONSE
        */

        echo json_encode([
            "status" => "ok",
            "message_id" => $message_id
        ]);
    }
    public function list()
    {
        $conversation_id = $_GET['conversation_id'] ?? null;

        if (!$conversation_id) {
            http_response_code(400);
            echo json_encode(["error" => "conversation_id required"]);
            return;
        }

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;

        $offset = ($page - 1) * $limit;

        $db = Database::connect();

        $stmt = $db->prepare("
            SELECT id, conversation_id, user_id, message, created_at
            FROM messages
            WHERE conversation_id = :conversation_id
            ORDER BY created_at DESC
            LIMIT :limit OFFSET :offset
        ");

        $stmt->bindValue(':conversation_id', $conversation_id, \PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);

        $stmt->execute();

        $messages = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        echo json_encode([
            "conversation_id" => (int)$conversation_id,
            "page" => $page,
            "limit" => $limit,
            "messages" => $messages
        ]);
    }
}