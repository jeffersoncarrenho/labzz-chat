<?php

namespace App\Middleware;

use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthMiddleware {

    public static function verify()
    {

        $headers = getallheaders();

        if(!isset($headers['Authorization'])){
            http_response_code(401);
            exit;
        }

        $token = str_replace("Bearer ","",$headers['Authorization']);

        try{

            $decoded = JWT::decode(
                $token,
                new Key($_ENV['JWT_SECRET'],"HS256")
            );

            return $decoded;

        }catch(Exception $e){

            http_response_code(401);
            exit;

        }

    }

}