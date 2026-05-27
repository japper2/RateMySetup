<?php
header("Content-Type: application/json");
require "db.php";

$setupId = (int) ($_GET["setup_id"] ?? 0);

if ($setupId <= 0) {
    echo json_encode(["success" => false, "message" => "Invalid setup"]);
    exit;
}

$stmt = $pdo->prepare("
    SELECT c.id, c.comment, c.created_at, u.name AS user_name
    FROM comments c
    JOIN users u ON u.id = c.user_id
    WHERE c.setup_id = ?
    ORDER BY c.created_at DESC
");
$stmt->execute([$setupId]);

echo json_encode(["success" => true, "comments" => $stmt->fetchAll()]);
