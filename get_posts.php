<?php
session_start();
require '../config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit();
}

try {
    // جلب المنشورات مع معلومات المستخدمين
    $stmt = $conn->prepare("
        SELECT p.*, u.username, u.profile_image 
        FROM posts p
        JOIN users u ON p.user_id = u.id
        ORDER BY p.created_at DESC
        LIMIT 20
    ");
    $stmt->execute();
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($posts);
} catch (PDOException $e) {
    echo json_encode([]);
}
?>