<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

require 'config.php';
require 'quotes.php';
require 'authors.php';
require 'categories.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

function respond($status, $data) {
    http_response_code($status);
    echo json_encode($data);
    exit;
}

$uri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

if (strpos($uri, '/api') === 0) {
    $endpoint = strtok(substr($uri, 4), '?');
    parse_str($_SERVER['QUERY_STRING'] ?? '', $params);

    if (strpos($endpoint, '/quotes') === 0) {
        handleQuotes($method, $params);
    } elseif (strpos($endpoint, '/authors') === 0) {
        handleAuthors($method, $params);
    } elseif (strpos($endpoint, '/categories') === 0) {
        handleCategories($method, $params);
    } else {
        respond(404, ['message' => 'Endpoint Not Found']);
    }
} else {
    respond(404, ['message' => 'API Endpoint Not Found']);
}
?>
