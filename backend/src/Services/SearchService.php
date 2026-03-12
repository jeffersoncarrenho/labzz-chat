<?php

namespace App\Services;

use Elastic\Elasticsearch\ClientBuilder;

class SearchService
{
    private $client;
    private $index = 'messages';

    public function __construct()
    {
        $this->client = ClientBuilder::create()
            ->setHosts(['http://localhost:9200'])
            ->build();
    }

    public function indexMessage(array $doc, $id = null)
    {
        $params = [
            'index' => $this->index,
            'body'  => $doc
        ];

        if ($id) {
            $params['id'] = $id;
        }

        $this->client->index($params);
    }

    public function searchMessages(string $query, int $size = 20)
    {
        $params = [
            'index' => $this->index,
            'body'  => [
                'query' => [
                    'multi_match' => [
                        'query'  => $query,
                        'fields' => ['message^2', 'conversation_id', 'user_id']
                    ]
                ],
                'size' => $size
            ]
        ];

        $res = $this->client->search($params);

        $hits = $res['hits']['hits'] ?? [];
        return array_map(fn($h) => $h['_source'] + ['_id' => $h['_id']], $hits);
    }
}