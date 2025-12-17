<?php
session_start();
<?php
require 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];

    try {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['profile_image'] = $user['profile_image'];
            $_SESSION['logged_in'] = true;
            
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "البريد الإلكتروني أو كلمة المرور غير صحيحة";
        }
    } catch(PDOException $e) {
        $error = "حدث خطأ أثناء تسجيل الدخول: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تسجيل الدخول</title>
    <!-- نفس التنسيقات السابقة -->
</head>
<body>
    <div class="container">
        <h2>تسجيل الدخول</h2>
        
        <?php if (!empty($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="email">البريد الإلكتروني:</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="password">كلمة المرور:</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit">تسجيل الدخول</button>
        </form>
        
        <div class="register-link">
            ليس لديك حساب؟ <a href="register.php">إنشاء حساب جديد</a>
        </div>
    </div>
</body>
</html>
<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require 'config.php';
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>لوحة التحكم</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .header {
            background: #007bff;
            color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .welcome {
            text-align: center;
            margin-bottom: 20px;
        }
        .logout {
            color: white;
            text-decoration: none;
            background: #dc3545;
            padding: 5px 10px;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>لوحة التحكم</h2>
        <a href="logout.php" class="logout">تسجيل الخروج</a>
    </div>
    
    <div class="container">
        <div class="welcome">
            <h2>مرحباً <?php echo htmlspecialchars($_SESSION['username']); ?></h2>
            <p>لقد سجلت دخولك بنجاح إلى نظامنا</p>
        </div>
    </div>
</body>
</html>
<?php
session_start();
session_unset();
session_destroy();

header("Location: login.php");
exit();
?>