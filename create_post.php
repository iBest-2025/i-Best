<?php
session_start();
require '../config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'غير مسجل الدخول']);
    exit();
}

$user_id = $_SESSION['user_id'];
$text = $_POST['text'] ?? '';

// معالجة رفع الصورة
$image_path = '';
if (isset($_FILES['image']) {
    $upload_dir = '../uploads/posts/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    $file_name = time() . '_' . basename($_FILES['image']['name']);
    $target_file = $upload_dir . $file_name;
    
    if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
        $image_path = 'uploads/posts/' . $file_name;
    }
}

// إدخال المنشور في قاعدة البيانات
try {
    $stmt = $conn->prepare("INSERT INTO posts (user_id, content, image_path, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$user_id, $text, $image_path]);
    
    echo json_encode(['success' => true, 'message' => 'تم نشر المنشور بنجاح']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'حدث خطأ في قاعدة البيانات']);
}
?>