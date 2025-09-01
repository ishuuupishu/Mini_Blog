<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/helpers.php';
require_once __DIR__ . '/../../classes/Post.php';

$user_id = require_login();
$title = $_POST['title'] ?? '';
$body  = $_POST['body'] ?? '';
$fileInfo = $_FILES['file'] ?? null;

$path = $type = null;
$size = null;
if ($fileInfo) {
    [$ok, $msg, $path, $type, $size] = handle_upload($fileInfo);
    if (!$ok) json_response(false, ['message' => $msg], 422);
}

$post = new Post($conn);
$ok = $post->create($title, $body, (int)$user_id, $path, $type, $size);
json_response($ok, ['message' => $ok ? 'Post created.' : 'Failed to create post.']);
