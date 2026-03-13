<?php

use PHPUnit\Framework\TestCase;

class MessageTest extends TestCase
{

    public function testSendMessage()
    {

        $data = [
            "conversation_id" => 1,
            "user_id" => 1,
            "message" => "Test message"
        ];

        $context = stream_context_create([
            "http" => [
                "method" => "POST",
                "header" => "Content-Type: application/json",
                "content" => json_encode($data)
            ]
        ]);

        $result = file_get_contents("http://localhost:8000/messages", false, $context);

        $response = json_decode($result,true);

        $this->assertEquals("ok",$response["status"]);

    }

}