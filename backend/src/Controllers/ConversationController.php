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

}