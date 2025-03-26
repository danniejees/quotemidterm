<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

require 'config.php';

$uri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

if (strpos($uri, '/api') === 0) {

    $endpoint = substr($uri, 4);

    if ($method === 'GET' && $endpoint === '/quotes') {
        $stmt = $pdo->query('SELECT quotes.id, quote, authors.author, categories.category
                             FROM quotes
                             JOIN authors ON quotes.author_id = authors.id
                             JOIN categories ON quotes.category_id = categories.id');
        $quotes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($quotes);
    }

    if ($method === 'GET' && isset($_GET['id']) && $endpoint === '/quotes') {
        $id = (int)$_GET['id'];
        $stmt = $pdo->prepare('SELECT quotes.id, quote, authors.author, categories.category
                               FROM quotes
                               JOIN authors ON quotes.author_id = authors.id
                               JOIN categories ON quotes.category_id = categories.id
                               WHERE quotes.id = ?');
        $stmt->execute([$id]);
        $quote = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($quote) {
            echo json_encode($quote);
        } else {
            echo json_encode(['message' => 'No Quotes Found']);
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
        echo json_encode([
            'id' => $pdo->lastInsertId(),
            'quote' => $data['quote'],
            'author_id' => $data['author_id'],
            'category_id' => $data['category_id']
        ]);
    }

    if ($method === 'GET' && $endpoint === '/authors') {
        $stmt = $pdo->query('SELECT id, author FROM authors');
        $authors = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($authors);
    }

    if ($method === 'GET' && isset($_GET['id']) && $endpoint === '/authors') {
        $id = (int)$_GET['id'];
        $stmt = $pdo->prepare('SELECT id, author FROM authors WHERE id = ?');
        $stmt->execute([$id]);
        $author = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($author) {
            echo json_encode($author);
        } else {
            echo json_encode(['message' => 'author_id Not Found']);
        }
    }

    if ($method === 'POST' && $endpoint === '/authors') {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!isset($data['author'])) {
            echo json_encode(['message' => 'Missing Required Parameters']);
            exit;
        }

        $stmt = $pdo->prepare('INSERT INTO authors (author) VALUES (?)');
        $stmt->execute([$data['author']]);
        echo json_encode([
            'id' => $pdo->lastInsertId(),
            'author' => $data['author']
        ]);
    }

    if ($method === 'GET' && $endpoint === '/categories') {
        $stmt = $pdo->query('SELECT id, category FROM categories');
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($categories);
    }

    if ($method === 'GET' && isset($_GET['id']) && $endpoint === '/categories') {
        $id = (int)$_GET['id'];
        $stmt = $pdo->prepare('SELECT id, category FROM categories WHERE id = ?');
        $stmt->execute([$id]);
        $category = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($category) {
            echo json_encode($category);
        } else {
            echo json_encode(['message' => 'category_id Not Found']);
        }
    }

    if ($method === 'POST' && $endpoint === '/categories') {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!isset($data['category'])) {
            echo json_encode(['message' => 'Missing Required Parameters']);
            exit;
        }

        $stmt = $pdo->prepare('INSERT INTO categories (category) VALUES (?)');
        $stmt->execute([$data['category']]);
        echo json_encode([
            'id' => $pdo->lastInsertId(),
            'category' => $data['category']
        ]);
    }

} else {
    echo json_encode(['message' => 'API Endpoint Not Found']);
    http_response_code(404);
}
