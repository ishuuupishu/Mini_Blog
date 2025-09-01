<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/helpers.php';
require_once __DIR__ . '/../../classes/User.php';

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

$user = new User($conn);
[$ok, $msg] = $user->register($username, $password);
json_response($ok, ['message' => $msg]);
