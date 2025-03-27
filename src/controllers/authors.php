<?php
require_once '../models/database.php';

function handleGetAuthors() {
    global $pdo;
    $id = isset($_GET['id']) ? $_GET['id'] : null;

    if ($id && !is_numeric($id)) {
        http_response_code(400);
        echo json_encode(['message' => 'Invalid author ID']);
        return;
    }

    $query = 'SELECT * FROM authors WHERE 1=1';
    $params = [];

    if ($id) {
        $query .= ' AND id = :id';
        $params[':id'] = $id;
    }

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($results)) {
        http_response_code(404);
        echo json_encode(['message' => 'No Authors Found']);
    } else {
        echo json_encode($results);
    }
}
?>
