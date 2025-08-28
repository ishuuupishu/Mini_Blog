<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/helpers.php';
require_once __DIR__ . '/../../classes/User.php';

$u = new User($conn);
$me = $u->whoami();
json_response(true, ['user' => $me]);
