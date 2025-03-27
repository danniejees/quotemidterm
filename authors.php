<?php
function handleAuthors($method, $params) {
    global $pdo;

    if ($method === 'GET') {
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

    if ($method === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        if (empty($data) || !isset($data['author'])) {
            respond(400, ['message' => 'Missing Required Parameters']);
        }

        $stmt = $pdo->prepare('INSERT INTO authors (author) VALUES (?)');
        $stmt->execute([$data['author']]);

        respond(201, ['id' => $pdo->lastInsertId(), 'author' => $data['author']]);
    }

    if ($method === 'PUT') {
        $data = json_decode(file_get_contents('php://input'), true);
        if (empty($data) || !isset($data['id'], $data['author'])) {
            respond(400, ['message' => 'Missing Required Parameters']);
        }

        $stmt = $pdo->prepare('SELECT id FROM authors WHERE id = ?');
        $stmt->execute([$data['id']]);
        if (!$stmt->fetch()) {
            respond(404, ['message' => 'author_id Not Found']);
        }

        $stmt = $pdo->prepare('UPDATE authors SET author = ? WHERE id = ?');
        $stmt->execute([$data['author'], $data['id']]);

        respond(200, ['id' => $data['id'], 'author' => $data['author']]);
    }

    if ($method === 'DELETE') {
        $data = json_decode(file_get_contents('php://input'), true);
        if (empty($data) || !isset($data['id'])) {
            respond(400, ['message' => 'Missing Required Parameters']);
        }

        $stmt = $pdo->prepare('DELETE FROM authors WHERE id = ?');
        $stmt->execute([$data['id']]);

        if ($stmt->rowCount() === 0) {
            respond(404, ['message' => 'author_id Not Found']);
        }

        respond(200, ['id' => $data['id']]);
    }

    respond(405, ['message' => 'Method Not Allowed']);
}
?>
