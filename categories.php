<?php
function handleCategories($method, $params) {
    global $pdo;

    if ($method === 'GET') {
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

    if ($method === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!isset($data['category'])) {
            respond(400, ['message' => 'Missing Required Parameters']);
        }

        $stmt = $pdo->prepare('INSERT INTO categories (category) VALUES (?)');
        $stmt->execute([$data['category']]);

        respond(201, ['id' => $pdo->lastInsertId(), 'category' => $data['category']]);
    }

    if ($method === 'PUT') {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!isset($data['id'], $data['category'])) {
            respond(400, ['message' => 'Missing Required Parameters']);
        }

        $stmt = $pdo->prepare('SELECT id FROM categories WHERE id = ?');
        $stmt->execute([$data['id']]);
        if (!$stmt->fetch()) {
            respond(404, ['message' => 'category_id Not Found']);
        }

        $stmt = $pdo->prepare('UPDATE categories SET category = ? WHERE id = ?');
        $stmt->execute([$data['category'], $data['id']]);

        respond(200, ['id' => $data['id'], 'category' => $data['category']]);
    }

    if ($method === 'DELETE') {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!isset($data['id'])) {
            respond(400, ['message' => 'Missing Required Parameters']);
        }

        $stmt = $pdo->prepare('DELETE FROM categories WHERE id = ?');
        $stmt->execute([$data['id']]);

        if ($stmt->rowCount() === 0) {
            respond(404, ['message' => 'category_id Not Found']);
        }

        respond(200, ['id' => $data['id']]);
    }

    respond(405, ['message' => 'Method Not Allowed']);
}
?>
