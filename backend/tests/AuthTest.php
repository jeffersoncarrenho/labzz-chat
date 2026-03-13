<?php

use PHPUnit\Framework\TestCase;

class AuthTest extends TestCase
{

    public function testLogin()
    {

        $data = [
            "email" => "jefferson@email.com",
            "password" => "123"
        ];

        $context = stream_context_create([
            "http" => [
                "method" => "POST",
                "header" => "Content-Type: application/json",
                "content" => json_encode($data)
            ]
        ]);

        $result = file_get_contents("http://localhost:8000/login", false, $context);

        $response = json_decode($result,true);

        $this->assertArrayHasKey("token",$response);

    }

}