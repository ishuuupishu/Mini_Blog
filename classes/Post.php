<?php
class Post {
    private mysqli $conn;

    public function __construct(mysqli $conn) {
        $this->conn = $conn;
    }

    public function create(string $title, string $body, int $user_id, ?string $file_path, ?string $file_type, ?int $file_size): bool {
        $title = trim($title);
        $body  = trim($body);
        if ($title === '' || $body === '') return false;
        $stmt = $this->conn->prepare('INSERT INTO posts (title, body, user_id, file_path, file_type, file_size) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('ssissi', $title, $body, $user_id, $file_path, $file_type, $file_size);
        return $stmt->execute();
    }

    public function all(): array {
        $sql = 'SELECT p.id, p.title, p.body, p.created_at, p.user_id, p.file_path, p.file_type, p.file_size, u.username
                FROM posts p
                LEFT JOIN users u ON u.id = p.user_id
                ORDER BY p.created_at DESC';
        $res = $this->conn->query($sql);
        $rows = [];
        while ($row = $res->fetch_assoc()) $rows[] = $row;
        return $rows;
    }

    public function find(int $id): ?array {
        $stmt = $this->conn->prepare('SELECT * FROM posts WHERE id=?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        return $row ?: null;
    }

    public function findOwnerId(int $post_id): ?int {
        $stmt = $this->conn->prepare('SELECT user_id FROM posts WHERE id = ?');
        $stmt->bind_param('i', $post_id);
        $stmt->execute();
        $stmt->bind_result($owner_id);
        if ($stmt->fetch()) return (int)$owner_id;
        return null;
    }

    public function update(int $id, string $title, string $body, int $user_id, ?string $file_path, ?string $file_type, ?int $file_size): bool {
        $owner = $this->findOwnerId($id);
        if ($owner === null || $owner !== $user_id) return false;
        $title = trim($title);
        $body  = trim($body);
        if ($title === '' || $body === '') return false;

        if ($file_path !== null) {
            // delete old file if any
            $curr = $this->find($id);
            if ($curr && !empty($curr['file_path'])) {
                $fileOnDisk = __DIR__ . '/../' . $curr['file_path'];
                if (is_file($fileOnDisk)) @unlink($fileOnDisk);
            }
            $stmt = $this->conn->prepare('UPDATE posts SET title=?, body=?, file_path=?, file_type=?, file_size=? WHERE id=?');
            $stmt->bind_param('sssiii', $title, $body, $file_path, $file_type, $file_size, $id);
        } else {
            $stmt = $this->conn->prepare('UPDATE posts SET title=?, body=? WHERE id=?');
            $stmt->bind_param('ssi', $title, $body, $id);
        }
        return $stmt->execute();
    }

    public function delete(int $id, int $user_id): bool {
        $owner = $this->findOwnerId($id);
        if ($owner === null || $owner !== $user_id) return false;
        // Delete file if exists
        $post = $this->find($id);
        if ($post && !empty($post['file_path'])) {
            $fileOnDisk = __DIR__ . '/../' . $post['file_path'];
            if (is_file($fileOnDisk)) @unlink($fileOnDisk);
        }
        $stmt = $this->conn->prepare('DELETE FROM posts WHERE id = ?');
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }
}
