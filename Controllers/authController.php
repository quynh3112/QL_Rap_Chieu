<?php
include "../Config/db.php";
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["status" => false, "message" => "Method không hỗ trợ"]);
    exit;
}

$d        = json_decode(file_get_contents("php://input"), true);
$username = $conn->real_escape_string($d['username'] ?? '');
$password = $d['password'] ?? '';

if (!$username || !$password) {
    echo json_encode(["status" => false, "message" => "Thiếu username hoặc mật khẩu"]);
    exit;
}

$res = $conn->query("SELECT * FROM Account WHERE username = '$username' LIMIT 1");
$user = $res ? $res->fetch_assoc() : null;

if (!$user || !password_verify($password, $user['password'])) {
    echo json_encode(["status" => false, "message" => "Sai tài khoản hoặc mật khẩu"]);
    exit;
}

// Không trả về password
unset($user['password']);

echo json_encode([
    "status" => true,
    "user"   => $user
]);

