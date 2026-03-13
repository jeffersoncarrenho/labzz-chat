<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

require_once __DIR__.'/../vendor/autoload.php';

use Dotenv\Dotenv;
use App\Core\Router;

/* carregar .env */

$dotenv = Dotenv::createImmutable(__DIR__.'/../');
$dotenv->load();

/* iniciar router */

$router = new Router();

/* carregar rotas */

require __DIR__.'/../routes/api.php';

/* dispatch */

$router->dispatch(
    $_SERVER['REQUEST_METHOD'],
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);