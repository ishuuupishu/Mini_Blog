<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/helpers.php';
require_once __DIR__ . '/../../classes/Post.php';

$user_id = require_login();
$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
if ($id <= 0) json_response(false, ['message' => 'Invalid post id.'], 422);

$post = new Post($conn);
$ok = $post->delete($id, (int)$user_id);
json_response($ok, ['message' => $ok ? 'Post deleted.' : 'Delete failed (not owner or invalid).']);
