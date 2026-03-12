<?php

namespace App\Controllers;

use App\Core\Database;
use PDO;

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

}