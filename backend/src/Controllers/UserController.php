<?php

namespace App\Controllers;
use OpenApi\Attributes as OA;
use App\Core\Database;

class UserController {

    #[OA\Post(
        path: "/users",
        tags: ["Users"],
        summary: "Create a new user",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name","email","password"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "John Doe"),
                    new OA\Property(property: "email", type: "string", example: "john@email.com"),
                    new OA\Property(property: "password", type: "string", example: "secret123")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "User created"
            )
        ]
    )]

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