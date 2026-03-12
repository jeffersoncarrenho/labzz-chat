<?php
namespace App\Controllers;
use App\Core\Database;
use PDO;

use Firebase\JWT\JWT;

class AuthController {

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