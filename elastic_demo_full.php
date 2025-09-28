<?php
require 'vendor/autoload.php';

use Elastic\Elasticsearch\ClientBuilder;

$client = ClientBuilder::create()
    ->setHosts(['localhost:9200']) // ES host
    ->build();

// ========== 1. CREATE INDEX (with static mapping) ==========
$params = [
    'index' => 'books_static',
    'body'  => [
        'mappings' => [
            'properties' => [
                'title'          => ['type' => 'text'],
                'author'         => ['type' => 'keyword'],
                'price'          => ['type' => 'float'],
                'published_date' => ['type' => 'date'],
                'tags'           => ['type' => 'keyword']
            ]
        ]
    ]
];

$response = $client->indices()->create($params);
print_r($response->asArray());


// ========== 2. INSERT DOCUMENT (CREATE) ==========
$params = [
    'index' => 'books_static',
    'id'    => 1,
    'body'  => [
        'title'          => 'Elasticsearch Basics',
        'author'         => 'John Doe',
        'price'          => 29.99,
        'published_date' => '2023-05-01',
        'tags'           => ['search', 'nosql']
    ]
];
$response = $client->index($params);
print_r($response->asArray());


// ========== 3. READ DOCUMENT ==========
$response = $client->get([
    'index' => 'books_static',
    'id'    => 1
]);
print_r($response->asArray());


// ========== 4. UPDATE DOCUMENT ==========
$response = $client->update([
    'index' => 'books_static',
    'id'    => 1,
    'body'  => [
        'doc' => [
            'price' => 34.99
        ]
    ]
]);
print_r($response->asArray());


// ========== 5. SEARCH DOCUMENTS ==========
$response = $client->search([
    'index' => 'books_static',
    'body'  => [
        'query' => [
            'match' => [
                'title' => 'Elasticsearch'
            ]
        ]
    ]
]);
print_r($response->asArray());


// ========== 6. PAGINATION ==========
$response = $client->search([
    'index' => 'books_static',
    'body'  => [
        'from' => 0,
        'size' => 5,
        'query' => [ 'match_all' => (object)[] ]
    ]
]);
print_r($response->asArray());


// ========== 7. BULK INSERT ==========
$params = [
    'body' => [
        [ 'index' => [ '_index' => 'books_static', '_id' => 2 ] ],
        [ 'title' => 'Advanced Elasticsearch', 'author' => 'Jane Smith', 'price' => 49.99, 'published_date' => '2022-08-15' ],

        [ 'index' => [ '_index' => 'books_static', '_id' => 3 ] ],
        [ 'title' => 'Search Engines', 'author' => 'Mike Lee', 'price' => 39.50, 'published_date' => '2021-02-10' ]
    ]
];
$response = $client->bulk($params);
print_r($response->asArray());


// ========== 8. CUSTOM ANALYZER DEMO ==========
$params = [
    'index' => 'books_analyzer',
    'body' => [
        'settings' => [
            'analysis' => [
                'analyzer' => [
                    'my_custom_analyzer' => [
                        'type' => 'custom',
                        'tokenizer' => 'standard',
                        'filter' => ['lowercase', 'stop']
                    ]
                ]
            ]
        ],
        'mappings' => [
            'properties' => [
                'content' => [
                    'type' => 'text',
                    'analyzer' => 'my_custom_analyzer'
                ]
            ]
        ]
    ]
];
$response = $client->indices()->create($params);
print_r($response->asArray());


// Test analyzer
$response = $client->indices()->analyze([
    'index' => 'books_analyzer',
    'body' => [
        'analyzer' => 'my_custom_analyzer',
        'text'     => 'The Quick Brown Fox Jumps Over The Lazy Dog'
    ]
]);
print_r($response->asArray());

?>
