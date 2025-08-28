<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/helpers.php';
require_once __DIR__ . '/../../classes/User.php';

$user = new User($conn);
$user->logout();
json_response(true, ['message' => 'Logged out.']);
