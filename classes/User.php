<?php
class User {
    private mysqli $conn;

    public function __construct(mysqli $conn) {
        $this->conn = $conn;
    }

    public function register(string $username, string $password): array {
        $username = trim($username);
        if ($username === '' || $password === '') {
            return [false, 'Username and password are required.'];
        }
        // unique
        $stmt = $this->conn->prepare('SELECT id FROM users WHERE username=? LIMIT 1');
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            return [false, 'Username already taken.'];
        }
        $stmt->close();

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $ins = $this->conn->prepare('INSERT INTO users (username, password) VALUES (?, ?)');
        $ins->bind_param('ss', $username, $hash);
        if ($ins->execute()) return [true, 'Registration successful.'];
        return [false, 'Registration failed.'];
    }

    public function login(string $username, string $password): array {
        $stmt = $this->conn->prepare('SELECT id, username, password FROM users WHERE username=? LIMIT 1');
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $res = $stmt->get_result();
        $user = $res->fetch_assoc();
        if (!$user) return [false, 'User not found.'];
        if (!password_verify($password, $user['password'])) return [false, 'Invalid credentials.'];
        if (session_status() === PHP_SESSION_NONE) session_start();
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['username'] = $user['username'];
        return [true, 'Login successful.'];
    }

    public function logout(): void {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params['path'], $params['domain'],
                $params['secure'], $params['httponly']
            );
        }
        session_destroy();
    }

    public function whoami(): ?array {
        if (!isset($_SESSION['user_id'])) return null;
        return ['id' => (int)$_SESSION['user_id'], 'username' => (string)$_SESSION['username']];
    }
}
