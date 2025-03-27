<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

require 'config.php';
require 'authors.php';
require 'categories.php';

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

if (strpos($uri, '/api') === 0) {
    $endpoint = strtok(substr($uri, 4), '?');
    parse_str($_SERVER['QUERY_STRING'] ?? '', $params);

    if (strpos($endpoint, '/quotes') === 0) {
        if ($method === 'GET') {
            $sql = 'SELECT quotes.id, quote, authors.author, categories.category FROM quotes 
                    JOIN authors ON quotes.author_id = authors.id 
                    JOIN categories ON quotes.category_id = categories.id';
            $conditions = [];
            $values = [];

            if (isset($params['id'])) {
                $conditions[] = 'quotes.id = ?';
                $values[] = (int)$params['id'];
            }
            if (isset($params['author_id'])) {
                $conditions[] = 'quotes.author_id = ?';
                $values[] = (int)$params['author_id'];
            }
            if (isset($params['category_id'])) {
                $conditions[] = 'quotes.category_id = ?';
                $values[] = (int)$params['category_id'];
            }

            if (!empty($conditions)) {
                $sql .= ' WHERE ' . implode(' AND ', $conditions);
            }

            $stmt = $pdo->prepare($sql);
            $stmt->execute($values);
            $quotes = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($quotes) {
                respond(200, count($quotes) === 1 && isset($params['id']) ? $quotes[0] : $quotes);
            } else {
                respond(404, ['message' => 'No Quotes Found']);
            }
        } elseif ($method === 'POST' || $method === 'PUT' || $method === 'DELETE') {
            include 'quotes.php';
        } else {
            respond(405, ['message' => 'Method Not Allowed']);
        }
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
