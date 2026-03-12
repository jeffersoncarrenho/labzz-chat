<?php

namespace App\Controllers;

use App\Core\Database;
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

        echo json_encode([
            "status" => "ok",
            "message_id" => $message_id
        ]);
    }
}