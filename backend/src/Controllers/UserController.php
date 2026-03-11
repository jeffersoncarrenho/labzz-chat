<?php

class UserController {

    public function create()
    {

        $data = json_decode(file_get_contents("php://input"),true);

        $db = Database::connect();

        $stmt = $db->prepare("
            INSERT INTO users (name,email,password)
            VALUES (?,?,?)
        ");

        $stmt->execute([
            $data['name'],
            $data['email'],
            password_hash($data['password'],PASSWORD_BCRYPT)
        ]);

        echo json_encode(["status"=>"user created"]);

    }

}