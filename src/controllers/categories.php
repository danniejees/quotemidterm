<?php
require_once '../models/database.php';

function handleGetCategories() {
    global $pdo;
    $id = isset($_GET['id']) ? $_GET['id'] : null;

    $query = 'SELECT * FROM categories WHERE 1=1';
    $params = [];

    if ($id && !is_numeric($id)) {
        echo json_encode(['message' => 'Invalid category_id']);
        return;
    }

    if ($id) {
        $query .= ' AND id = :id';
        $params[':id'] = $id;
    }

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($results)) {
        echo json_encode(['message' => 'No Categories Found']);
    } else {
        echo json_encode($results);
    }
}

function handlePostCategory() {
    global $pdo;
    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['category'])) {
        echo json_encode(['message' => 'Missing Required Parameters']);
        return;
    }

    $category = $input['category'];

    $checkStmt = $pdo->prepare('SELECT id FROM categories WHERE category = :category');
    $checkStmt->execute([':category' => $category]);
    if ($checkStmt->fetchColumn()) {
        echo json_encode(['message' => 'Category already exists']);
        return;
    }

    $stmt = $pdo->prepare('INSERT INTO categories (category) VALUES (:category) RETURNING id');
    $stmt->execute([':category' => $category]);
    $category_id = $stmt->fetchColumn();

    echo json_encode(['id' => $category_id, 'category' => $category]);
}

function handlePutCategory() {
    global $pdo;
    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['id'], $input['category'])) {
        echo json_encode(['message' => 'Missing Required Parameters']);
        return;
    }

    $id = $input['id'];
    $category = $input['category'];

    $checkStmt = $pdo->prepare('SELECT id FROM categories WHERE id = :id');
    $checkStmt->execute([':id' => $id]);
    if (!$checkStmt->fetchColumn()) {
        echo json_encode(['message' => 'category_id Not Found']);
        return;
    }

    $stmt = $pdo->prepare('UPDATE categories SET category = :category WHERE id = :id');
    $stmt->execute([':category' => $category, ':id' => $id]);

    echo json_encode(['id' => $id, 'category' => $category]);
}

function handleDeleteCategory() {
    global $pdo;

    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        echo json_encode(['message' => 'category_id Not Found']);
        return;
    }

    $id = $_GET['id'];
    $stmt = $pdo->prepare('DELETE FROM categories WHERE id = :id');
    $stmt->execute([':id' => $id]);

    if ($stmt->rowCount() === 0) {
        echo json_encode(['message' => 'No Categories Found']);
    } else {
        echo json_encode(['id' => $id]);
    }
}
?>
