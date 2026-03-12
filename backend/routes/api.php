<?php

use App\Controllers\UserController;
use App\Controllers\AuthController;
use App\Controllers\MessageController;
use App\Controllers\ConversationController;

$router->add("POST","/users",[UserController::class,"create"]);
$router->add("POST","/login",[AuthController::class,"login"]);
$router->add("POST","/messages",[MessageController::class,"send"]);
$router->add("GET","/messages",[MessageController::class,"list"]);
$router->add("POST","/conversations",[ConversationController::class,"create"]);