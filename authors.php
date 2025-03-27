<?php
function handleAuthors($method, $params) {
    global $pdo;

    if ($method === 'GET') {
        if (isset($params['id'])) {
            $stmt = $pdo->prepare('SELECT * FROM authors WHERE id = ?');
            $stmt->execute([$params['id']]);
            $author = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$author) {
                respond(404, ['message' => 'author_id Not Found']);
            }
            respond(200, $author);
        } else {
            $stmt = $pdo->query('SELECT * FROM authors LIMIT 25');
            $authors = $stmt->fetchAll(PDO::FETCH_ASSOC);
            respond(200, $authors);
        }
    }

    if ($method === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        if (empty($data['author'])) {
            respond(400, ['message' => 'Missing Required Parameters']);
        }
        $stmt = $pdo->prepare('INSERT INTO authors (author) VALUES (?)');
        $stmt->execute([$data['author']]);
        respond(201, ['id' => $pdo->lastInsertId(), 'author' => $data['author']]);
    }

    if ($method === 'PUT') {
        $data = json_decode(file_get_contents('php://input'), true);
        if (empty($data['id']) || empty($data['author'])) {
            respond(400, ['message' => 'Missing Required Parameters']);
        }
        $stmt = $pdo->prepare('UPDATE authors SET author = ? WHERE id = ?');
        $stmt->execute([$data['author'], $data['id']]);
        respond(200, ['id' => $data['id'], 'author' => $data['author']]);
    }

    if ($method === 'DELETE') {
        $data = json_decode(file_get_contents('php://input'), true);
        if (empty($data['id'])) {
            respond(400, ['message' => 'Missing Required Parameters']);
        }
        $stmt = $pdo->prepare('DELETE FROM authors WHERE id = ?');
        $stmt->execute([$data['id']]);
        if ($stmt->rowCount() === 0) {
            respond(404, ['message' => 'author_id Not Found']);
        }
        respond(200, ['id' => $data['id']]);
    }
}

// categories.php
function handleCategories($method, $params) {
    global $pdo;

    if ($method === 'GET') {
        if (isset($params['id'])) {
            $stmt = $pdo->prepare('SELECT * FROM categories WHERE id = ?');
            $stmt->execute([$params['id']]);
            $category = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$category) {
                respond(404, ['message' => 'category_id Not Found']);
            }
            respond(200, $category);
        } else {
            $stmt = $pdo->query('SELECT * FROM categories LIMIT 25');
            $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
            respond(200, $categories);
        }
    }

    if ($method === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        if (empty($data['category'])) {
            respond(400, ['message' => 'Missing Required Parameters']);
        }
        $stmt = $pdo->prepare('INSERT INTO categories (category) VALUES (?)');
        $stmt->execute([$data['category']]);
        respond(201, ['id' => $pdo->lastInsertId(), 'category' => $data['category']]);
    }

    if ($method === 'PUT') {
        $data = json_decode(file_get_contents('php://input'), true);
        if (empty($data['id']) || empty($data['category'])) {
            respond(400, ['message' => 'Missing Required Parameters']);
        }
        $stmt = $pdo->prepare('UPDATE categories SET category = ? WHERE id = ?');
        $stmt->execute([$data['category'], $data['id']]);
        respond(200, ['id' => $data['id'], 'category' => $data['category']]);
    }

    if ($method === 'DELETE') {
        $data = json_decode(file_get_contents('php://input'), true);
        if (empty($data['id'])) {
            respond(400, ['message' => 'Missing Required Parameters']);
        }
        $stmt = $pdo->prepare('DELETE FROM categories WHERE id = ?');
        $stmt->execute([$data['id']]);
        if ($stmt->rowCount() === 0) {
            respond(404, ['message' => 'category_id Not Found']);
        }
        respond(200, ['id' => $data['id']]);
    }
}
?>
