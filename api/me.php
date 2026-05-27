<?php
header("Content-Type: application/json");
require "db.php";
require "auth.php";

$userId = currentUserId();

if (!$userId) {
    echo json_encode([
        "logged_in" => false,
        "user_id" => null,
        "name" => null,
        "profile_image" => null
    ]);
    exit;
}

$stmt = $pdo->prepare("SELECT id, name, profile_image FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    session_destroy();
    echo json_encode([
        "logged_in" => false,
        "user_id" => null,
        "name" => null,
        "profile_image" => null
    ]);
    exit;
}

$_SESSION["name"] = $user["name"];

echo json_encode([
    "logged_in" => true,
    "user_id" => (int) $user["id"],
    "name" => $user["name"],
    "profile_image" => $user["profile_image"]
]);
