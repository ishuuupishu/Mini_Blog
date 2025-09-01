<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/helpers.php';
require_once __DIR__ . '/../../classes/User.php';

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

$user = new User($conn);
[$ok, $msg] = $user->login($username, $password);
if ($ok) {
    json_response(true, [
        'message' => $msg,
        'user' => ['id' => current_user_id(), 'username' => $_SESSION['username']]
    ]);
}
json_response(false, ['message' => $msg], 401);
