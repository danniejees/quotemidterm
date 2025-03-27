<?php
function handleQuotes($method, $params) {
    global $pdo;

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
    }

    if ($method === 'POST') {
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

    if ($method === 'PUT') {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!isset($data['id'], $data['quote'], $data['author_id'], $data['category_id'])) {
            respond(400, ['message' => 'Missing Required Parameters']);
        }

        $stmt = $pdo->prepare('SELECT id FROM quotes WHERE id = ?');
        $stmt->execute([$data['id']]);
        if (!$stmt->fetch()) {
            respond(404, ['message' => 'No Quotes Found']);
        }

        $stmt = $pdo->prepare('UPDATE quotes SET quote = ?, author_id = ?, category_id = ? WHERE id = ?');
        $stmt->execute([$data['quote'], $data['author_id'], $data['category_id'], $data['id']]);

        respond(200, ['id' => $data['id'], 'quote' => $data['quote'], 'author_id' => $data['author_id'], 'category_id' => $data['category_id']]);
    }

    if ($method === 'DELETE') {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!isset($data['id'])) {
            respond(400, ['message' => 'Missing Required Parameters']);
        }

        $stmt = $pdo->prepare('DELETE FROM quotes WHERE id = ?');
        $stmt->execute([$data['id']]);

        if ($stmt->rowCount() === 0) {
            respond(404, ['message' => 'No Quotes Found']);
        }

        respond(200, ['id' => $data['id']]);
    }

    respond(405, ['message' => 'Method Not Allowed']);
}
?>
