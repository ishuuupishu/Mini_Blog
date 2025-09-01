<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/helpers.php';
require_once __DIR__ . '/../../classes/Post.php';

$post = new Post($conn);
$rows = $post->all();
$uid = current_user_id();

$data = array_map(function ($r) use ($uid) {
    $mime = $r['file_type'] ?? null;
    return [
        'id' => (int)$r['id'],
        'title' => htmlspecialchars($r['title'], ENT_QUOTES, 'UTF-8'),
        'body' => nl2br(htmlspecialchars($r['body'], ENT_QUOTES, 'UTF-8')),
        'created_at' => $r['created_at'],
        'author' => $r['username'] ?? 'unknown',
        'user_id' => (int)$r['user_id'],
        'can_edit' => $uid && ((int)$r['user_id'] === (int)$uid),
        'file' => [
            'path' => $r['file_path'] ?: null,
            'type' => $mime,
            'size' => isset($r['file_size']) ? (int)$r['file_size'] : null
        ]
    ];
}, $rows);

json_response(true, ['posts' => $data]);
