<?php
header("Content-Type: application/json");
require "db.php";
require "auth.php";

$userId = requireLogin();
$setupId = (int) ($_POST["setup_id"] ?? 0);
$comment = trim($_POST["comment"] ?? "");

if ($setupId <= 0 || $comment === "") {
    echo json_encode(["success" => false, "message" => "Comment is required"]);
    exit;
}

if (strlen($comment) > 1000) {
    echo json_encode(["success" => false, "message" => "Comment is too long"]);
    exit;
}

$stmt = $pdo->prepare("INSERT INTO comments (setup_id, user_id, comment) VALUES (?, ?, ?)");
$stmt->execute([$setupId, $userId, $comment]);

echo json_encode(["success" => true]);
