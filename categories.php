<?php
function handleCategories($method, $params) {
    global $pdo;

    if ($method === 'GET') {
        $sql = 'SELECT id, category FROM categories';
        $values = [];

        if (isset($params['id'])) {
            $sql .= ' WHERE id = ?';
            $values[] = (int)$params['id'];
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($values);
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($categories)) {
            respond(404, ['message' => 'category_id Not Found']);
        } else if (isset($params['id'])) {
            respond(200, $categories[0]);
        } else {
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

        if ($stmt->rowCount() === 0) {
            respond(404, ['message' => 'category_id Not Found']);
        }

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
