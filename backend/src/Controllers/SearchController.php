<?php

namespace App\Controllers;

use App\Services\SearchService;
use OpenApi\Attributes as OA;

class SearchController
{

    #[OA\Get(
        path: "/search",
        tags: ["Search"],
        summary: "Search messages using Elasticsearch",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "query",
                in: "query",
                required: true,
                description: "Search term",
                schema: new OA\Schema(type: "string")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Search results"
            )
        ]
    )]
    public function messages()
    {
        $q = $_GET['q'] ?? null;

        if (!$q) {
            http_response_code(400);
            echo json_encode(['error' => 'query parameter q required']);
            return;
        }

        $service = new SearchService();
        $results = $service->searchMessages($q);

        echo json_encode([
            'query'   => $q,
            'results' => $results
        ]);
    }
}