<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/helpers.php';
require_once __DIR__ . '/../../classes/Post.php';

$user_id = require_login();
$title = $_POST['title'] ?? '';
$body  = $_POST['body'] ?? '';

$post = new Post($conn);
$ok = $post->create($title, $body, (int)$user_id);
json_response($ok, ['message' => $ok ? 'Post created.' : 'Failed to create post.']);
