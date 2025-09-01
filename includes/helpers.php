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

function generate_filename(string $original): string {
    $ext = pathinfo($original, PATHINFO_EXTENSION);
    $rand = bin2hex(random_bytes(8));
    return date('Ymd_His_') . $rand . ($ext ? ('.' . strtolower($ext)) : '');
}

function handle_upload(array $file): array {
    // Returns [ok(bool), message(string), path(string|null), mime(string|null), size(int|null)]
    if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return [true, 'No file uploaded', null, null, null];
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return [false, 'Upload error', null, null, null];
    }
    $maxSize = 15 * 1024 * 1024; // 15MB
    if ($file['size'] > $maxSize) {
        return [false, 'File too large (max 15MB)', null, null, null];
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']) ?: 'application/octet-stream';

    $allowed = [
        // images
        'image/jpeg','image/png','image/gif','image/webp',
        // videos
        'video/mp4','video/webm',
        // docs
        'application/pdf'
    ];

    if (!in_array($mime, $allowed, true)) {
        return [false, 'Unsupported file type', null, null, null];
    }

    $safeName = generate_filename($file['name']);
    $destDir = __DIR__ . '/../uploads';
    if (!is_dir($destDir)) @mkdir($destDir, 0755, true);
    $destPath = $destDir . '/' . $safeName;
    if (!move_uploaded_file($file['tmp_name'], $destPath)) {
        return [false, 'Failed to save uploaded file', null, null, null];
    }
    // web path
    $webPath = 'uploads/' . $safeName;
    return [true, 'Uploaded', $webPath, $mime, (int)$file['size']];
}
