<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function currentUserId(): ?int
{
    return isset($_SESSION["user_id"]) ? (int) $_SESSION["user_id"] : null;
}

function requireLogin(): int
{
    $userId = currentUserId();

    if (!$userId) {
        http_response_code(401);
        header("Content-Type: application/json");
        echo json_encode([
            "success" => false,
            "message" => "You must be logged in first"
        ]);
        exit;
    }

    return $userId;
}
