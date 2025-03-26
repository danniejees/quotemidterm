<?php
header('Content-Type: application/json');
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && $_SERVER['REQUEST_URI'] === '/api/quotes') {
    $stmt = $pdo->query('SELECT quotes.id, quote, authors.author, categories.category
                         FROM quotes
                         JOIN authors ON quotes.author_id = authors.id
                         JOIN categories ON quotes.category_id = categories.id');
    $quotes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($quotes);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare('SELECT quotes.id, quote, authors.author, categories.category
                           FROM quotes
                           JOIN authors ON quotes.author_id = authors.id
                           JOIN categories ON quotes.category_id = categories.id
                           WHERE quotes.id = ?');
    $stmt->execute([$id]);
    $quote = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($quote) {
        echo json_encode($quote);
    } else {
        echo json_encode(['message' => 'No Quotes Found']);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SERVER['REQUEST_URI'] === '/api/quotes') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!isset($data['quote'], $data['author_id'], $data['category_id'])) {
        echo json_encode(['message' => 'Missing Required Parameters']);
        exit;
    }

    $stmt = $pdo->prepare('INSERT INTO quotes (quote, author_id, category_id) VALUES (?, ?, ?)');
    $stmt->execute([$data['quote'], $data['author_id'], $data['category_id']]);
    echo json_encode([
        'id' => $pdo->lastInsertId(),
        'quote' => $data['quote'],
        'author_id' => $data['author_id'],
        'category_id' => $data['category_id']
    ]);
}


if ($_SERVER['REQUEST_METHOD'] === 'GET' && $_SERVER['REQUEST_URI'] === '/api/authors') {
    $stmt = $pdo->query('SELECT id, author FROM authors');
    $authors = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($authors);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare('SELECT id, author FROM authors WHERE id = ?');
    $stmt->execute([$id]);
    $author = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($author) {
        echo json_encode($author);
    } else {
        echo json_encode(['message' => 'author_id Not Found']);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SERVER['REQUEST_URI'] === '/api/authors') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!isset($data['author'])) {
        echo json_encode(['message' => 'Missing Required Parameters']);
        exit;
    }

    $stmt = $pdo->prepare('INSERT INTO authors (author) VALUES (?)');
    $stmt->execute([$data['author']]);
    echo json_encode([
        'id' => $pdo->lastInsertId(),
        'author' => $data['author']
    ]);
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT' && $_SERVER['REQUEST_URI'] === '/api/authors') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!isset($data['id'], $data['author'])) {
        echo json_encode(['message' => 'Missing Required Parameters']);
        exit;
    }

    $stmt = $pdo->prepare('UPDATE authors SET author = ? WHERE id = ?');
    $stmt->execute([$data['author'], $data['id']]);
    echo json_encode([
        'id' => $data['id'],
        'author' => $data['author']
    ]);
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($_GET['id']) && $_SERVER['REQUEST_URI'] === '/api/authors') {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare('DELETE FROM authors WHERE id = ?');
    $stmt->execute([$id]);
    if ($stmt->rowCount() > 0) {
        echo json_encode(['id' => $id]);
    } else {
        echo json_encode(['message' => 'author_id Not Found']);
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'GET' && $_SERVER['REQUEST_URI'] === '/api/categories') {
    $stmt = $pdo->query('SELECT id, category FROM categories');
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($categories);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare('SELECT id, category FROM categories WHERE id = ?');
    $stmt->execute([$id]);
    $category = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($category) {
        echo json_encode($category);
    } else {
        echo json_encode(['message' => 'category_id Not Found']);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SERVER['REQUEST_URI'] === '/api/categories') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!isset($data['category'])) {
        echo json_encode(['message' => 'Missing Required Parameters']);
        exit;
    }

    $stmt = $pdo->prepare('INSERT INTO categories (category) VALUES (?)');
    $stmt->execute([$data['category']]);
    echo json_encode([
        'id' => $pdo->lastInsertId(),
        'category' => $data['category']
    ]);
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT' && $_SERVER['REQUEST_URI'] === '/api/categories') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!isset($data['id'], $data['category'])) {
        echo json_encode(['message' => 'Missing Required Parameters']);
        exit;
    }

    $stmt = $pdo->prepare('UPDATE categories SET category = ? WHERE id = ?');
    $stmt->execute([$data['category'], $data['id']]);
    echo json_encode([
        'id' => $data['id'],
        'category' => $data['category']
    ]);
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($_GET['id']) && $_SERVER['REQUEST_URI'] === '/api/categories') {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare('DELETE FROM categories WHERE id = ?');
    $stmt->execute([$id]);
    if ($stmt->rowCount() > 0) {
        echo json_encode(['id' => $id]);
    } else {
        echo json_encode(['message' => 'category_id Not Found']);
    }
}
