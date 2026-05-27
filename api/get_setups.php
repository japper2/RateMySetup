<?php
header("Content-Type: application/json");
require "db.php";
require "auth.php";

$userId = currentUserId() ?? 0;
$order = $_GET["order"] ?? "latest";
$category = trim($_GET["category"] ?? "");

$columnStmt = $pdo->prepare("SHOW COLUMNS FROM setups LIKE 'category'");
$columnStmt->execute();
$hasCategory = (bool) $columnStmt->fetch();
$categorySelect = $hasCategory ? "s.category" : "'General' AS category";
$categoryWhere = $hasCategory ? "LOWER(s.category) = LOWER(:category)" : "LOWER('General') = LOWER(:category)";

$orderSql = "s.created_at DESC";
if ($order === "rated") {
    $orderSql = "average_rating DESC, s.created_at DESC";
} elseif ($order === "liked") {
    $orderSql = "likes_count DESC, s.created_at DESC";
}

$sql = "
    SELECT
        s.id,
        s.user_id,
        s.title,
        s.description,
        s.image_path,
        $categorySelect,
        s.created_at,
        u.name AS user_name,
        u.profile_image AS user_profile_image,
        COALESCE(ROUND(AVG(r.score), 1), 0) AS average_rating,
        COUNT(DISTINCT r.id) AS ratings_count,
        COUNT(DISTINCT l.id) AS likes_count,
        COUNT(DISTINCT c.id) AS comments_count,
        MAX(CASE WHEN l.user_id = :user_id_like THEN 1 ELSE 0 END) AS liked_by_me,
        MAX(CASE WHEN r.user_id = :user_id_rating THEN r.score ELSE NULL END) AS my_rating
    FROM setups s
    JOIN users u ON u.id = s.user_id
    LEFT JOIN ratings r ON r.setup_id = s.id
    LEFT JOIN likes l ON l.setup_id = s.id
    LEFT JOIN comments c ON c.setup_id = s.id
    WHERE (:category = '' OR $categoryWhere)
    GROUP BY s.id, s.user_id, s.title, s.description, s.image_path, s.created_at, u.name, u.profile_image" . ($hasCategory ? ", s.category" : "") . "
    ORDER BY $orderSql
";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    ":user_id_like" => $userId,
    ":user_id_rating" => $userId,
    ":category" => $category,
]);

$setups = $stmt->fetchAll();

echo json_encode(["success" => true, "setups" => $setups]);
