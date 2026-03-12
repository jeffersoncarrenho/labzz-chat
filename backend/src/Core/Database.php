<?php

namespace App\Core;

use PDO;

class Database {

    public static function connect()
    {
        return new PDO(
            "mysql:host=localhost;dbname=labzz_chat",
            "chat",
            "chat123",
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]
        );
    }

}