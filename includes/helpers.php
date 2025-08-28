<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function sanitize($value) {
    return htmlspecialchars(trim((string)$value), ENT_QUOTES, 'UTF-8');
}

function json_response($success, $data = [], $code = 200) {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'data'    => $data,
    ]);
    exit;
}

function current_user_id() {
    return $_SESSION['user_id'] ?? null;
}

function require_login() {
    $uid = current_user_id();
    if (!$uid) {
        json_response(false, ['message' => 'Authentication required.'], 401);
    }
    return $uid;
}
