<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

require 'config.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];
parse_str($_SERVER['QUERY_STRING'] ?? '', $params);

if ($method === 'OPTIONS') {
    http_response_code(200);
    exit;
}

function respond($status, $data) {
    http_response_code($status);
    echo json_encode($data);
    exit;
}

if (strpos($uri, '/api') !== 0) {
    respond(404, ['message' => 'API Endpoint Not Found']);
}

$endpoint = substr($uri, 4);

if ($method === 'GET' && strpos($endpoint, '/quotes') === 0) {
    $sql = 'SELECT quotes.id AS id, quotes.quote AS quote, authors.author AS author, categories.category AS category 
            FROM quotes 
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
        if (count($quotes) === 1 && isset($params['id'])) {
            respond(200, $quotes[0]);
        }
        respond(200, $quotes);
    } else {
        respond(200, ['message' => 'No Quotes Found']);
    }
}

if ($method === 'GET' && strpos($endpoint, '/authors') === 0) {
    if (isset($params['id'])) {
        $stmt = $pdo->prepare('SELECT * FROM authors WHERE id = ?');
        $stmt->execute([(int)$params['id']]);
        $author = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($author) {
            respond(200, $author);
        } else {
            respond(200, ['message' => 'author_id Not Found']);
        }
    } else {
        $stmt = $pdo->query('SELECT * FROM authors');
        $authors = $stmt->fetchAll(PDO::FETCH_ASSOC);
        respond(200, $authors);
    }
}

if ($method === 'GET' && strpos($endpoint, '/categories') === 0) {
    if (isset($params['id'])) {
        $stmt = $pdo->prepare('SELECT * FROM categories WHERE id = ?');
        $stmt->execute([(int)$params['id']]);
        $category = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($category) {
            respond(200, $category);
        } else {
            respond(200, ['message' => 'category_id Not Found']);
        }
    } else {
        $stmt = $pdo->query('SELECT * FROM categories');
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        respond(200, $categories);
    }
}

respond(404, ['message' => 'Endpoint Not Found']);
