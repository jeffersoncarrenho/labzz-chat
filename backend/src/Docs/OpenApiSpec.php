<?php

namespace App\Docs;

use OpenApi\Attributes as OA;

#[OA\OpenApi(
    security: [
        ["bearerAuth" => []]
    ]
)]

#[OA\Info(
    title: "Labzz Realtime Chat API",
    version: "1.0.0",
    description: "Backend API for a realtime chat platform built for the Labzz technical challenge."
)]

#[OA\Server(
    url: "http://localhost:8000",
    description: "Local development server"
)]

#[OA\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "http",
    scheme: "bearer",
    bearerFormat: "JWT",
    description: "JWT authentication using Bearer token"
)]

class OpenApiSpec {}