<?php
header("Content-Type: application/json");
require "db.php";
require "auth.php";

$userId = requireLogin();

if (!isset($_FILES["profile_image"]) || $_FILES["profile_image"]["error"] !== UPLOAD_ERR_OK) {
    echo json_encode(["success" => false, "message" => "Choose a profile image first"]);
    exit;
}

$allowed = [
    "image/jpeg" => "jpg",
    "image/png" => "png",
    "image/webp" => "webp",
    "image/gif" => "gif"
];

$mime = mime_content_type($_FILES["profile_image"]["tmp_name"]);

if (!isset($allowed[$mime])) {
    echo json_encode(["success" => false, "message" => "Only JPG, PNG, WEBP and GIF images are allowed"]);
    exit;
}

$uploadDir = __DIR__ . "/../uploads/profiles";
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$filename = "profile_" . $userId . "_" . uniqid("", true) . "." . $allowed[$mime];
$target = $uploadDir . "/" . $filename;

if (!move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target)) {
    echo json_encode(["success" => false, "message" => "Profile image upload failed"]);
    exit;
}

$path = "uploads/profiles/" . $filename;

$stmt = $pdo->prepare("UPDATE users SET profile_image = ? WHERE id = ?");
$stmt->execute([$path, $userId]);
$_SESSION["profile_image"] = $path;

echo json_encode(["success" => true, "profile_image" => $path]);
