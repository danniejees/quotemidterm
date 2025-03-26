<?php
require_once '../models/database.php';

function handleGetAuthors() {
    global $pdo;
    $id = isset($_GET['id']) ? $_GET['id'] : null;

    $query = 'SELECT * FROM authors WHERE 1=1';
    $params = [];

    if ($id && is_numeric($id)) {
        $query .= ' AND id = :id';
        $params[':id'] = $id;
    }

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($results)) {
        echo json_encode(['message' => 'No Authors Found']);
    } else {
        echo json_encode($results);
    }
}

function handlePostAuthor() {
    global $pdo;
    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['author'])) {
        echo json_encode(['message' => 'Missing Required Parameters']);
        return;
    }

    $author = $input['author'];

    $stmt = $pdo->prepare('INSERT INTO authors (author) VALUES (:author) RETURNING id');
    $stmt->execute([':author' => $author]);
    $author_id = $stmt->fetchColumn();

    echo json_encode(['id' => $author_id, 'author' => $author]);
}

function handlePutAuthor() {
    global $pdo;
    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['id'], $input['author'])) {
        echo json_encode(['message' => 'Missing Required Parameters']);
        return;
    }

    $id = $input['id'];
    $author = $input['author'];

    $checkStmt = $pdo->prepare('SELECT id FROM authors WHERE id = :id');
    $checkStmt->execute([':id' => $id]);
    $existingAuthor = $checkStmt->fetch(PDO::FETCH_ASSOC);

    if (!$existingAuthor) {
        echo json_encode(['message' => 'author_id Not Found']);
        return;
    }

    $stmt = $pdo->prepare('UPDATE authors SET author = :author WHERE id = :id');
    $stmt->execute([':author' => $author, ':id' => $id]);

    echo json_encode(['id' => $id, 'author' => $author]);
}

function handleDeleteAuthor() {
    global $pdo;

    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        echo json_encode(['message' => 'author_id Not Found']);
        return;
    }

    $id = $_GET['id'];
    $stmt = $pdo->prepare('DELETE FROM authors WHERE id = :id');
    $stmt->execute([':id' => $id]);

    if ($stmt->rowCount() === 0) {
        echo json_encode(['message' => 'No Authors Found']);
    } else {
        echo json_encode(['id' => $id]);
    }
}
?>
