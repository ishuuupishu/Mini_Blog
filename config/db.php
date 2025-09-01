<?php
// Database connection (MySQLi)
$DB_HOST = 'localhost';
$DB_USER = 'root';       // XAMPP default
$DB_PASS = '';           // XAMPP default empty password
$DB_NAME = 'blog_mini';  // UPDATED database name

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($conn->connect_error) {
    http_response_code(500);
    die('Database connection failed: ' . $conn->connect_error);
}
$conn->set_charset('utf8mb4');

// Ensure uploads directory exists
$uploadsDir = __DIR__ . '/../uploads';
if (!is_dir($uploadsDir)) {
    @mkdir($uploadsDir, 0755, true);
}
