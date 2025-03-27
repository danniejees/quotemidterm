<?php
function handleAuthors($method, $params) {
    global $pdo;

    if ($method === 'GET') {
        $sql = 'SELECT * FROM authors';
        $values = [];

        if (isset($params['id'])) {
            $sql .= ' WHERE id = ?';
            $values[] = (int)$params['id'];
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($values);
        $authors = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($authors)) {
            respond(404, ['message' => 'author_id Not Found']);
        } else if (isset($params['id'])) {
            respond(200, $authors[0]);
        } else {
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

        if ($stmt->rowCount() === 0) {
            respond(404, ['message' => 'author_id Not Found']);
        }

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
?>
