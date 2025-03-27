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
@@ -21,84 +20,104 @@
    exit;
}

if (strpos($uri, '/api') === 0) {
    $endpoint = strtok(substr($uri, 4), '?');
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
            respond(200, count($quotes) === 1 && isset($params['id']) ? $quotes[0] : $quotes);
        } else {
            respond(404, ['message' => 'No Quotes Found']);
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
                respond(404, ['message' => 'author_id Not Found']);
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
                respond(404, ['message' => 'category_id Not Found']);
            }
        } else {
            $stmt = $pdo->query('SELECT * FROM categories');
            $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
            respond(200, $categories);
        }




    }


    if ($method === 'POST' && $endpoint === '/quotes') {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!isset($data['quote'], $data['author_id'], $data['category_id'])) {
            respond(400, ['message' => 'Missing Required Parameters']);
        }

        $stmt = $pdo->prepare('SELECT id FROM authors WHERE id = ?');
        $stmt->execute([$data['author_id']]);
        if (!$stmt->fetch()) {
            respond(404, ['message' => 'author_id Not Found']);
        }

        $stmt = $pdo->prepare('SELECT id FROM categories WHERE id = ?');
        $stmt->execute([$data['category_id']]);
        if (!$stmt->fetch()) {
            respond(404, ['message' => 'category_id Not Found']);
        }

        $stmt = $pdo->prepare('INSERT INTO quotes (quote, author_id, category_id) VALUES (?, ?, ?)');
        $stmt->execute([$data['quote'], $data['author_id'], $data['category_id']]);

        respond(201, ['id' => $pdo->lastInsertId(), 'quote' => $data['quote'], 'author_id' => $data['author_id'], 'category_id' => $data['category_id']]);
    }


    respond(404, ['message' => 'Endpoint Not Found']);
} else {
    respond(404, ['message' => 'API Endpoint Not Found']);
}
