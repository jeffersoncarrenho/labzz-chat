<?php

use App\Controllers\UserController;
use App\Controllers\AuthController;
use App\Controllers\MessageController;
use App\Controllers\ConversationController;
use App\Controllers\SearchController;

$router->add("POST","/users",[UserController::class,"create"]);
$router->add("POST","/login",[AuthController::class,"login"]);

$router->add("POST","/messages",[MessageController::class,"send"]);
$router->add("GET","/messages",[MessageController::class,"list"]);
$router->add("POST","/conversations",[ConversationController::class,"create"]);
$router->add("GET","/messages",[MessageController::class,"list"]);
$router->add("GET","/conversations",[ConversationController::class,"list"]);
$router->add("POST","/participants",[ConversationController::class,"addParticipant"]);

$router->add("GET", "/search", [SearchController::class, "messages"]);