<?php

$router->add("POST","/users",[UserController::class,"create"]);
$router->add("POST","/login",[AuthController::class,"login"]);