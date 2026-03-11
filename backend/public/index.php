<?php

require_once __DIR__.'/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/../');
$dotenv->load();

require "../src/Core/Router.php";
require "../src/Core/Database.php";
require "../src/Controllers/UserController.php";
require "../src/Controllers/AuthController.php";


$router = new Router();

require "../routes/api.php";

$router->dispatch(
    $_SERVER['REQUEST_METHOD'],
    parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH)
);

var_dump($_ENV['JWT_SECRET']);