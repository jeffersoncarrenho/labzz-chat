<?php

namespace App\Controllers;

use App\Core\Database;
use PDO;
use OpenApi\Attributes as OA;

class ConversationController {

    public function create()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        $type = $data['type'] ?? 'private';

        $db = Database::connect();

        $stmt = $db->prepare("
            INSERT INTO conversations (type)
            VALUES (?)
        ");

        $stmt->execute([$type]);

        $id = $db->lastInsertId();

        echo json_encode([
            "conversation_id"=>$id
        ]);
    }

    public function list()
    {
        $user_id = $_GET['user_id'] ?? null;

        if (!$user_id) {
            http_response_code(400);
            echo json_encode(["error" => "user_id required"]);
            return;
        }

        $db = Database::connect();

        $stmt = $db->prepare("
            SELECT c.id, c.type, c.created_at
            FROM conversations c
            JOIN conversation_participants cp
            ON cp.conversation_id = c.id
            WHERE cp.user_id = ?
            ORDER BY c.created_at DESC
        ");

        $stmt->execute([$user_id]);

        $conversations = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            "conversations" => $conversations
        ]);
    }

    public function addParticipant()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        $conversation_id = $data['conversation_id'];
        $user_id = $data['user_id'];

        $db = Database::connect();

        $stmt = $db->prepare("
            INSERT INTO conversation_participants
            (conversation_id, user_id)
            VALUES (?, ?)
        ");

        $stmt->execute([$conversation_id,$user_id]);

        echo json_encode(["status"=>"ok"]);
    }

    #[OA\Get(
        path: "/conversations/{id}/messages",
        tags: ["Messages"],
        summary: "Get messages from a conversation",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            ),
            new OA\Parameter(
                name: "page",
                in: "query",
                required: false,
                schema: new OA\Schema(type: "integer", example: 1)
            ),
            new OA\Parameter(
                name: "limit",
                in: "query",
                required: false,
                schema: new OA\Schema(type: "integer", example: 20)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "List of messages"
            )
        ]
    )]

    public function messages($id)
    {
        $page = $_GET['page'] ?? 1;
        $limit = $_GET['limit'] ?? 20;

        $offset = ($page - 1) * $limit;

        $db = Database::connect();

        $stmt = $db->prepare("
            SELECT m.id, m.user_id, m.message, m.created_at
            FROM messages m
            WHERE m.conversation_id = ?
            ORDER BY m.created_at DESC
            LIMIT ? OFFSET ?
        ");

        $stmt->execute([$id, $limit, $offset]);

        $messages = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        echo json_encode([
            "conversation_id" => $id,
            "page" => (int)$page,
            "limit" => (int)$limit,
            "messages" => $messages
        ]);
    }

}