<?php
require_once '../models/database.php';

function handleGetQuotes() {
    global $pdo;
    $id = isset($_GET['id']) ? $_GET['id'] : null;
    $author_id = isset($_GET['author_id']) ? $_GET['author_id'] : null;
    $category_id = isset($_GET['category_id']) ? $_GET['category_id'] : null;

    if ($id && !is_numeric($id)) {
        echo json_encode(['message' => 'Invalid quote ID']);
        return;
    }

    if ($author_id && !is_numeric($author_id)) {
        echo json_encode(['message' => 'Invalid author ID']);
        return;
    }

    if ($category_id && !is_numeric($category_id)) {
        echo json_encode(['message' => 'Invalid category ID']);
        return;
    }

    $query = 'SELECT id, quote, author_id, category_id FROM quotes WHERE 1=1';
    $params = [];

    if ($id) {
        $query .= ' AND id = :id';
        $params[':id'] = $id;
    }
    if ($author_id) {
        $query .= ' AND author_id = :author_id';
        $params[':author_id'] = $author_id;
    }
    if ($category_id) {
        $query .= ' AND category_id = :category_id';
        $params[':category_id'] = $category_id;
    }

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($results)) {
        echo json_encode(['message' => 'No Quotes Found']);
    } else {
        echo json_encode($results);
    }
}
?>
