<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/helpers.php';
require_once __DIR__ . '/../../classes/Post.php';

$user_id = require_login();
$id    = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$title = $_POST['title'] ?? '';
$body  = $_POST['body'] ?? '';
if ($id <= 0) json_response(false, ['message' => 'Invalid post id.'], 422);

// handle optional new file
$path = $type = null;
$size = null;
if (isset($_FILES['file']) && $_FILES['file']['error'] !== UPLOAD_ERR_NO_FILE) {
    [$ok, $msg, $path, $type, $size] = handle_upload($_FILES['file']);
    if (!$ok) json_response(false, ['message' => $msg], 422);
}

$post = new Post($conn);
$ok = $post->update($id, $title, $body, (int)$user_id, $path, $type, $size);
json_response($ok, ['message' => $ok ? 'Post updated.' : 'Update failed (not owner or invalid).']);
