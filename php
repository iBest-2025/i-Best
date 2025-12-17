<?php
session_start();

// التحقق من أن المستخدم مسجل الدخول
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
<?php
session_start();

$host = "localhost";
$dbname = "user_authentication";
$username = "root"; // استخدم اسم المستخدم الخاص بك
$password = ""; // استخدم كلمة المرور الخاصة بك

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->exec("SET NAMES utf8");
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// دالة لحماية المدخلات
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}
?>

require 'config.php';

// جلب بيانات المستخدم من قاعدة البيانات
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// إذا لم يتم العثور على المستخدم
if (!$user) {
    session_destroy();
    header("Location: login.php");
    exit();
}

// جلب عدد الإشعارات غير المقروءة
$notification_count = 0;
$notifications = [];
/*
// يمكنك تفعيل هذا الجزء بعد إنشاء جدول الإشعارات
$stmt = $conn->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
$stmt->execute([$user_id]);
$notification_count = $stmt->fetchColumn();
*/
?>

<!DOCTYPE html>
<html lang="ar">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>𝐼 𝔅𝔢𝔰𝔱 - <?php echo htmlspecialchars($user['username']); ?></title>
  <style>
    /* ضع هنا كل أنماط CSS الخاصة بك من الملف الأصلي */
    /* ... */
  </style>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<!-- استبدل البيانات الثابتة بمتغيرات PHP -->
<header>𝐼 𝔅𝔢𝔰𝔱 - مرحباً <?php echo htmlspecialchars($user['username']); ?></header>

<nav>
  <a href="profile.php?id=<?php echo $user['id']; ?>" class="nav-link"><span class="icon">👤</span> الملف الشخصي</a>
  <a href="dashboard.php" class="nav-link active"><span class="icon">🏠</span> الرئيسية</a>
  <a href="groups.php" class="nav-link"><span class="icon">👥</span> المجموعات</a>
  <a href="editProfile.php" class="nav-link"><span class="icon">⚙️</span> الإعدادات</a>
  <a href="messages.php" class="nav-link"><span class="icon">💬</span> الرسائل</a>
  <a href="notifications.php" class="nav-link"><span class="icon"> الإشعارات 📢 
    <?php if ($notification_count > 0): ?>
      <div class="notification-badge" id="notificationBadge"><?php echo $notification_count; ?></div>
    <?php endif; ?>
  </a>
  <a href="logout.php" class="nav-link"><span class="icon">🚪</span> تسجيل الخروج</a>
</nav>

<!-- باقي محتوى الـ dashboard الخاص بك -->
<div class="content">
  <!-- ... -->
</div>

<script>
// استبدال دوال localStorage بطلبات AJAX للخادم

// مثال: إنشاء منشور جديد
function createPost() {
    const text = document.getElementById("newPostText").value.trim();
    const imageInput = document.getElementById("postImage");
    
    if (!text && !imageInput.files[0]) {
        showAlert("الرجاء إدخال نص أو رفع صورة", "error");
        return;
    }
    
    const formData = new FormData();
    formData.append('text', text);
    if (imageInput.files[0]) {
        formData.append('image', imageInput.files[0]);
    }
    
    fetch('api/create_post.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert("تم نشر المنشور بنجاح");
            loadPosts(); // إعادة تحميل المنشورات
        } else {
            showAlert(data.message, "error");
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert("حدث خطأ أثناء نشر المنشور", "error");
    });
}

// دالة لجلب المنشورات من الخادم
function loadPosts() {
    fetch('api/get_posts.php')
    .then(response => response.json())
    .then(posts => {
        const container = document.getElementById('homePostsContainer');
        container.innerHTML = '';
        
        if (posts.length === 0) {
            container.innerHTML = '<p style="text-align:center; color:#888; padding:20px;">لا توجد منشورات حالياً.</p>';
            return;
        }
        
        posts.forEach(post => {
            // إنشاء عناصر المنشورات هنا كما في الكود الأصلي
            // مع تعديل البيانات لاستخدام post من الخادم
        });
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

// تهيئة الصفحة عند التحميل
document.addEventListener('DOMContentLoaded', function() {
    loadPosts();
    updateNotificationCount();
});

// دالة لتحديث عدد الإشعارات
function updateNotificationCount() {
    fetch('api/get_notification_count.php')
    .then(response => response.json())
    .then(data => {
        const badge = document.getElementById('notificationBadge');
        if (data.count > 0) {
            badge.textContent = data.count;
            badge.style.display = 'flex';
        } else {
            badge.style.display = 'none';
        }
    });
}
</script>

</body>
</html>