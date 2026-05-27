<?php
header("Content-Type: application/json");
require "db.php";
require "auth.php";

$userId = requireLogin();
$setupId = (int) ($_POST["setup_id"] ?? 0);

if ($setupId <= 0) {
    echo json_encode(["success" => false, "message" => "Invalid setup"]);
    exit;
}

$stmt = $pdo->prepare("SELECT id FROM likes WHERE setup_id = ? AND user_id = ? LIMIT 1");
$stmt->execute([$setupId, $userId]);
$like = $stmt->fetch();

if ($like) {
    $delete = $pdo->prepare("DELETE FROM likes WHERE setup_id = ? AND user_id = ?");
    $delete->execute([$setupId, $userId]);
    echo json_encode(["success" => true, "liked" => false]);
} else {
    $insert = $pdo->prepare("INSERT INTO likes (setup_id, user_id) VALUES (?, ?)");
    $insert->execute([$setupId, $userId]);
    echo json_encode(["success" => true, "liked" => true]);
}
