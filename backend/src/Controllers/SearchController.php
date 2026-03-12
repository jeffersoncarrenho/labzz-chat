<?php

namespace App\Controllers;

use App\Services\SearchService;

class SearchController
{
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