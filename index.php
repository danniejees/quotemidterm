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

if (strpos($uri, '/api') === 0) {
    $endpoint = substr($uri, 4);
    parse_str($_SERVER['QUERY_STRING'] ?? '', $params);

    if ($method === 'GET' && strpos($endpoint, '/quotes') === 0) {
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
            echo json_encode(count($quotes) === 1 && isset($params['id']) ? $quotes[0] : $quotes);
        } else {
            echo json_encode([]);
        }
    }

    if ($method === 'POST' && $endpoint === '/quotes') {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!isset($data['quote'], $data['author_id'], $data['category_id'])) {
            echo json_encode(['message' => 'Missing Required Parameters']);
            exit;
        }

        $stmt = $pdo->prepare('INSERT INTO quotes (quote, author_id, category_id) VALUES (?, ?, ?)');
        $stmt->execute([$data['quote'], $data['author_id'], $data['category_id']]);

        echo json_encode(['id' => $pdo->lastInsertId(), 'quote' => $data['quote'], 'author_id' => $data['author_id'], 'category_id' => $data['category_id']]);
    }

    if ($method === 'POST' && $endpoint === '/authors') {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!isset($data['author'])) {
            echo json_encode(['message' => 'Missing Required Parameters']);
            exit;
        }

        $stmt = $pdo->prepare('INSERT INTO authors (author) VALUES (?)');
        $stmt->execute([$data['author']]);

        echo json_encode(['id' => $pdo->lastInsertId(), 'author' => $data['author']]);
    }

    if ($method === 'POST' && $endpoint === '/categories') {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!isset($data['category'])) {
            echo json_encode(['message' => 'Missing Required Parameters']);
            exit;
        }

        $stmt = $pdo->prepare('INSERT INTO categories (category) VALUES (?)');
        $stmt->execute([$data['category']]);

        echo json_encode(['id' => $pdo->lastInsertId(), 'category' => $data['category']]);
    }

} else {
    echo json_encode(['message' => 'API Endpoint Not Found']);
    http_response_code(404);
}
