<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

require 'config.php';

$uri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

function respond($status, $data) {
    http_response_code($status);
    echo json_encode($data);
    exit;
}

function extractEndpoint($uri) {
    return strtok(substr($uri, 4), '?');
}

function extractParams() {
    parse_str($_SERVER['QUERY_STRING'] ?? '', $params);
    return $params;
}

if (strpos($uri, '/api') === 0) {
    $endpoint = extractEndpoint($uri);
    $params = extractParams();

    switch (true) {
        case (strpos($endpoint, '/quotes') === 0):
            require 'quotes.php';
            handleQuotes($method, $params);
            break;

        case (strpos($endpoint, '/authors') === 0):
            require 'authors.php';
            handleAuthors($method, $params);
            break;

        case (strpos($endpoint, '/categories') === 0):
            require 'categories.php';
            handleCategories($method, $params);
            break;

        default:
            respond(404, ['message' => 'Endpoint Not Found']);
    }
} else {
    respond(404, ['message' => 'API Endpoint Not Found']);
}
