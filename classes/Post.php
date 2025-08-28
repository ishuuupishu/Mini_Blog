<?php
class Post {
    private mysqli $conn;

    public function __construct(mysqli $conn) {
        $this->conn = $conn;
    }

    public function create(string $title, string $body, int $user_id): bool {
        $title = trim($title);
        $body  = trim($body);
        if ($title === '' || $body === '') return false;
        $stmt = $this->conn->prepare('INSERT INTO posts (title, body, user_id) VALUES (?, ?, ?)');
        $stmt->bind_param('ssi', $title, $body, $user_id);
        return $stmt->execute();
    }

    public function all(): array {
        $sql = 'SELECT p.id, p.title, p.body, p.created_at, p.user_id, u.username
                FROM posts p
                LEFT JOIN users u ON u.id = p.user_id
                ORDER BY p.created_at DESC';
        $res = $this->conn->query($sql);
        $rows = [];
        while ($row = $res->fetch_assoc()) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function findOwnerId(int $post_id): ?int {
        $stmt = $this->conn->prepare('SELECT user_id FROM posts WHERE id = ?');
        $stmt->bind_param('i', $post_id);
        $stmt->execute();
        $stmt->bind_result($owner_id);
        if ($stmt->fetch()) {
            return (int)$owner_id;
        }
        return null;
    }

    public function update(int $id, string $title, string $body, int $user_id): bool {
        $owner = $this->findOwnerId($id);
        if ($owner === null || $owner !== $user_id) return false;
        $title = trim($title);
        $body  = trim($body);
        if ($title === '' || $body === '') return false;
        $stmt = $this->conn->prepare('UPDATE posts SET title = ?, body = ? WHERE id = ?');
        $stmt->bind_param('ssi', $title, $body, $id);
        return $stmt->execute();
    }

    public function delete(int $id, int $user_id): bool {
        $owner = $this->findOwnerId($id);
        if ($owner === null || $owner !== $user_id) return false;
        $stmt = $this->conn->prepare('DELETE FROM posts WHERE id = ?');
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }
}
