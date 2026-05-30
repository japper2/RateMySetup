<?php
$host = "localhost";
$dbname = "ratemysetup";
$username = "root";
$password = "";

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
    try {
        $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'profile_image'");
        if (!$stmt->fetch()) {
            $pdo->exec("ALTER TABLE users ADD COLUMN profile_image VARCHAR(255) DEFAULT NULL");
        }
    } catch (Throwable $e) {}

    try {
        $stmt = $pdo->query("SHOW COLUMNS FROM setups LIKE 'category'");
        if (!$stmt->fetch()) {
            $pdo->exec("ALTER TABLE setups ADD COLUMN category VARCHAR(50) DEFAULT 'General'");
        }
    } catch (Throwable $e) {}

} catch (PDOException $e) {
    http_response_code(500);
    header("Content-Type: application/json");
    echo json_encode([
        "success" => false,
        "message" => "Database connection failed",
        "debug" => $e->getMessage()
    ]);
    exit;
}
