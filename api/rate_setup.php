<?php
header("Content-Type: application/json");
require "db.php";
require "auth.php";

$userId = requireLogin();
$setupId = (int) ($_POST["setup_id"] ?? 0);
$score = (int) ($_POST["score"] ?? 0);

if ($setupId <= 0 || $score < 1 || $score > 5) {
    echo json_encode(["success" => false, "message" => "Invalid rating"]);
    exit;
}

$stmt = $pdo->prepare("
    INSERT INTO ratings (setup_id, user_id, score)
    VALUES (?, ?, ?)
    ON DUPLICATE KEY UPDATE score = VALUES(score)
");
$stmt->execute([$setupId, $userId, $score]);

echo json_encode(["success" => true]);
