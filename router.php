<?php
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($request_uri === '/' || file_exists(__DIR__ . $request_uri)) {
    return false;
}

require_once __DIR__ . '/index.php';
