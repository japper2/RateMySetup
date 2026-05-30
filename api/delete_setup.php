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

$stmt = $pdo->prepare("SELECT id, user_id, image_path FROM setups WHERE id = ?");
$stmt->execute([$setupId]);
$setup = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$setup) {
    echo json_encode(["success" => false, "message" => "Setup not found"]);
    exit;
}

if ((int) $setup["user_id"] !== $userId) {
    http_response_code(403);
    echo json_encode(["success" => false, "message" => "You can only delete your own setup"]);
    exit;
}

$delete = $pdo->prepare("DELETE FROM setups WHERE id = ? AND user_id = ?");
$delete->execute([$setupId, $userId]);

$imagePath = $setup["image_path"] ?? "";
if ($imagePath && str_starts_with($imagePath, "uploads/")) {
    $file = __DIR__ . "/../" . $imagePath;
    if (is_file($file)) {
        @unlink($file);
    }
}

echo json_encode(["success" => true]);
