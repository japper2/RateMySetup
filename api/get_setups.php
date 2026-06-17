<?php
header("Content-Type: application/json");
require "db.php";
require "auth.php";

try {
    $userId = currentUserId() ?? 0;
    $order = $_GET["order"] ?? "latest";
    $category = trim($_GET["category"] ?? "");
    $search = trim($_GET["search"] ?? "");

    $columnStmt = $pdo->prepare("SHOW COLUMNS FROM setups LIKE 'category'");
    $columnStmt->execute();
    $hasCategory = (bool) $columnStmt->fetch(PDO::FETCH_ASSOC);

    $categorySelect = $hasCategory ? "s.category" : "'General' AS category";

    $where = [];
    $params = [
        ":liked_user_id" => $userId,
        ":rated_user_id" => $userId,
    ];

    if ($category !== "") {
        if ($hasCategory) {
            $where[] = "LOWER(s.category) = LOWER(:category_filter)";
            $params[":category_filter"] = $category;
        } else {
            $where[] = "LOWER('General') = LOWER(:category_filter)";
            $params[":category_filter"] = $category;
        }
    }

    if ($search !== "") {
        $searchParts = [
            "s.title LIKE :search_title",
            "s.description LIKE :search_description",
            "u.name LIKE :search_user"
        ];

        $term = "%" . $search . "%";
        $params[":search_title"] = $term;
        $params[":search_description"] = $term;
        $params[":search_user"] = $term;

        if ($hasCategory) {
            $searchParts[] = "s.category LIKE :search_category";
            $params[":search_category"] = $term;
        }

        $where[] = "(" . implode(" OR ", $searchParts) . ")";
    }

    $whereSql = $where ? "WHERE " . implode(" AND ", $where) : "";

    $orderSql = "s.created_at DESC";
    if ($order === "rated") {
        $orderSql = "average_rating DESC, s.created_at DESC";
    } elseif ($order === "liked") {
        $orderSql = "likes_count DESC, s.created_at DESC";
    }

    $groupBy = "s.id, s.user_id, s.title, s.description, s.image_path, s.created_at, u.name, u.profile_image";
    if ($hasCategory) {
        $groupBy .= ", s.category";
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
            MAX(CASE WHEN l.user_id = :liked_user_id THEN 1 ELSE 0 END) AS liked_by_me,
            MAX(CASE WHEN r.user_id = :rated_user_id THEN r.score ELSE NULL END) AS my_rating
        FROM setups s
        JOIN users u ON u.id = s.user_id
        LEFT JOIN ratings r ON r.setup_id = s.id
        LEFT JOIN likes l ON l.setup_id = s.id
        LEFT JOIN comments c ON c.setup_id = s.id
        $whereSql
        GROUP BY $groupBy
        ORDER BY $orderSql
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    echo json_encode([
        "success" => true,
        "setups" => $stmt->fetchAll(PDO::FETCH_ASSOC)
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Could not load setups",
        "debug" => $e->getMessage()
    ]);
}
