<?php
namespace App\Controllers;
use App\Core\Database;
use PDO;
use Firebase\JWT\JWT;
use OpenApi\Attributes as OA;

class AuthController {


    #[OA\Post(
        path: "/login",
        tags: ["Auth"],
        summary: "Authenticate user and return JWT token",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["email","password"],
                properties: [
                    new OA\Property(property: "email", type: "string", example: "user@email.com"),
                    new OA\Property(property: "password", type: "string", example: "secret123")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "JWT token returned",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "token", type: "string", example: "jwt_token_here")
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "Invalid credentials"
            )
        ]
    )]
    public function login()
    {

        $data = json_decode(file_get_contents("php://input"), true);

        $db = Database::connect();

        $stmt = $db->prepare("
            SELECT * FROM users WHERE email = ?
        ");

        $stmt->execute([$data['email']]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        //Agora só usuários autenticados podem acessar.
        //$user = AuthMiddleware::verify();

        if(!$user){
            http_response_code(401);
            echo json_encode(["error"=>"invalid credentials"]);
            return;
        }

        if(!password_verify($data['password'],$user['password'])){
            http_response_code(401);
            echo json_encode(["error"=>"invalid credentials"]);
            return;
        }

        $payload = [
            "user_id"=>$user['id'],
            "exp"=>time()+3600
        ];
        
        $secret = $_ENV['JWT_SECRET'];
        $token = JWT::encode($payload,$secret,"HS256");

        echo json_encode([
            "token"=>$token
        ]);

    }

}