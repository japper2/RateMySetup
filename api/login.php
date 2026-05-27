<?php
header("Content-Type: application/json");
require "db.php";
session_start();

$email = trim($_POST["email"] ?? "");
$password = $_POST["password"] ?? "";

if ($email === "" || $password === "") {
    echo json_encode(["success" => false, "message" => "Email and password are required"]);
    exit;
}

$stmt = $pdo->prepare("SELECT id, name, email, password_hash, profile_image FROM users WHERE email = ? LIMIT 1");
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user["password_hash"])) {
    echo json_encode(["success" => false, "message" => "Invalid email or password"]);
    exit;
}

$_SESSION["user_id"] = (int) $user["id"];
$_SESSION["name"] = $user["name"];
$_SESSION["profile_image"] = $user["profile_image"] ?? "";

echo json_encode(["success" => true, "name" => $user["name"]]);
