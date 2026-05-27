<?php
header("Content-Type: application/json");
require "db.php";
require "auth.php";

$userId = requireLogin();
$title = trim($_POST["title"] ?? "");
$description = trim($_POST["description"] ?? "");
$category = trim($_POST["category"] ?? "General");

if ($title === "" || $description === "") {
    echo json_encode(["success" => false, "message" => "Title and description are required"]);
    exit;
}

$imagePath = "assets/setup-preview.png";

if (isset($_FILES["image"]) && $_FILES["image"]["error"] === UPLOAD_ERR_OK) {
    $allowed = ["image/jpeg" => "jpg", "image/png" => "png", "image/webp" => "webp", "image/gif" => "gif"];
    $mime = mime_content_type($_FILES["image"]["tmp_name"]);

    if (!isset($allowed[$mime])) {
        echo json_encode(["success" => false, "message" => "Only JPG, PNG, WEBP and GIF images are allowed"]);
        exit;
    }

    $uploadDir = __DIR__ . "/../uploads";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $filename = uniqid("setup_", true) . "." . $allowed[$mime];
    $target = $uploadDir . "/" . $filename;

    if (!move_uploaded_file($_FILES["image"]["tmp_name"], $target)) {
        echo json_encode(["success" => false, "message" => "Image upload failed"]);
        exit;
    }

    $imagePath = "uploads/" . $filename;
}

$columnStmt = $pdo->prepare("SHOW COLUMNS FROM setups LIKE 'category'");
$columnStmt->execute();
$hasCategory = (bool) $columnStmt->fetch();

if ($hasCategory) {
    $stmt = $pdo->prepare("INSERT INTO setups (user_id, title, description, image_path, category) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$userId, $title, $description, $imagePath, $category]);
} else {
    $stmt = $pdo->prepare("INSERT INTO setups (user_id, title, description, image_path) VALUES (?, ?, ?, ?)");
    $stmt->execute([$userId, $title, $description, $imagePath]);
}

echo json_encode(["success" => true]);
