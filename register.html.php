<?php
require 'config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // التحقق من تطابق كلمات المرور
    if ($password !== $confirm_password) {
        $error = "كلمة المرور غير متطابقة";
    } else {
        try {
            // التحقق من عدم وجود مستخدم بنفس البريد الإلكتروني
            $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            
            if ($stmt->rowCount() > 0) {
                $error = "هذا البريد الإلكتروني مسجل بالفعل";
            } else {
                // رفع صورة الملف الشخصي إذا وجدت
                $profile_image = 'default.png';
                if (isset($_FILES['profile_image']) {
                    $upload_dir = 'uploads/profile_images/';
                    if (!file_exists($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }
                    
                    $file_name = time() . '_' . basename($_FILES['profile_image']['name']);
                    $target_file = $upload_dir . $file_name;
                    
                    if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_file)) {
                        $profile_image = $file_name;
                    }
                }
                
                // إضافة المستخدم الجديد
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO users (username, email, password, profile_image, created_at) VALUES (?, ?, ?, ?, NOW())");
                $stmt->execute([$username, $email, $hashed_password, $profile_image]);
                
                $success = "تم إنشاء الحساب بنجاح، جاري تحويلك إلى صفحة تسجيل الدخول...";
                
                // إعادة توجيه بعد 1.5 ثانية
                header("Refresh: 1.5; url=login.php");
            }
        } catch(PDOException $e) {
            $error = "حدث خطأ أثناء التسجيل: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
  <meta charset="UTF-8" />
  <title>إنشاء حساب - 𝐼 𝔅𝔢𝔰𝔱</title>
  <style>
    body {
      margin: 0;
      padding: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: white;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      direction: rtl;
    }

    .login-box {
      background-color: #fff;
      padding: 40px 30px;
      border-radius: 12px;
      box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
      width: 320px;
      text-align: center;
    }

    .logo-text {
      font-size: 40px;
      font-weight: bold;
      color: black;
      margin-bottom: 10px;
      letter-spacing: 1px;
      font-family: 'Arial', sans-serif;
    }

    h2 {
      margin-bottom: 20px;
      color: #444;
    }

    input {
      width: 100%;
      padding: 5px;
      margin: 5px 0;
      border: 2px solid #ccc;
      border-radius: 2px;
      font-size: 15px;
      transition: all 0.3s ease;
      background-color: #f9f9f9;
    }

    input:focus {
      border-color: #0072ff;
      outline: none;
      background-color: #fff;
      box-shadow: 0 0 5px rgba(0, 114, 255, 0.4);
    }

    button {
      width: 100%;
      padding: 14px;
      background-color: #0072ff;
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      cursor: pointer;
      margin-top: 10px;
      transition: 0.3s;
    }

    button:hover {
      background-color: #005fcc;
    }

    .error {
      color: red;
      margin-top: 10px;
      min-height: 20px;
    }

    .success {
      color: green;
      margin-top: 10px;
      min-height: 20px;
    }

    a {
      color: #0072ff;
      text-decoration: none;
      font-size: 14px;
    }

    a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

  <div class="login-box">
    <div class="logo-text">𝐼 𝔅𝔢𝔰𝔱</div>
    <h2>تسجيل حساب جديد</h2>

    <form method="POST" enctype="multipart/form-data">
      <input type="text" name="username" placeholder="اسم المستخدم" required />
      <input type="email" name="email" placeholder="البريد الإلكتروني" required />
      <input type="password" name="password" placeholder="كلمة المرور" required />
      <input type="password" name="confirm_password" placeholder="تأكيد كلمة المرور" required />
      <input type="file" name="profile_image" accept="image/*" style="margin-top: 10px;">
      
      <button type="submit">تسجيل</button>
    </form>

    <?php if (!empty($error)): ?>
      <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>
    
    <?php if (!empty($success)): ?>
      <p class="success">✅ <?php echo $success; ?></p>
    <?php endif; ?>

    <p>هل لديك حساب؟ <a href="login.php">تسجيل دخول</a></p>
  </div>

</body>
</html>