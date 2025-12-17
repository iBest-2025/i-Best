// دالة تسجيل المستخدم
function register(event) {
    // منع إعادة تحميل الصفحة عند التسجيل
    event.preventDefault();

    // جلب قيم الحقول من النموذج
    const username = document.getElementById("username").value.trim();
    const email = document.getElementById("email").value.trim().toLowerCase();
    const password = document.getElementById("password").value;
    const confirmPassword = document.getElementById("confirmPassword").value;

    // التحقق من صحة البيانات
    if (!validateForm(username, email, password, confirmPassword)) {
        return; // إيقاف التنفيذ إذا كانت البيانات غير صالحة
    }

    // جلب بيانات المستخدمين الحاليين من التخزين المحلي
    let users = JSON.parse(localStorage.getItem("users")) || [];

    // التحقق من عدم وجود مستخدم مسجل بنفس البريد
    if (isEmailRegistered(email, users)) {
        showMessage("هذا البريد الإلكتروني مسجل بالفعل!", "red");
        return;
    }

    // إنشاء كائن المستخدم الجديد
    const newUser = {
        id: generateUserId(),
        username,
        email,
        password: simpleEncrypt(password), // تشفير بسيط لكلمة المرور
        createdAt: new Date().toISOString()
    };

    // إضافة المستخدم الجديد إلى المصفوفة
    users.push(newUser);

    // حفظ المصفوفة المحدثة في التخزين المحلي
    localStorage.setItem("users", JSON.stringify(users));

    // عرض رسالة النجاح
    showMessage("✅ تم إنشاء الحساب بنجاح، جاري تحويلك...", "green");

    // إعادة تعيين النموذج
    event.target.reset();

    // التوجيه إلى لوحة التحكم بعد ثانيتين
    setTimeout(() => {
        window.location.href = "dashboard.html";
    }, 2000);
}

// دالة التحقق من صحة البيانات
function validateForm(username, email, password, confirmPassword) {
    // التحقق من اسم المستخدم
    if (username.length < 3) {
        showMessage("اسم المستخدم يجب أن يكون 3 أحرف على الأقل", "red");
        return false;
    }

    // التحقق من صحة البريد الإلكتروني
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        showMessage("البريد الإلكتروني غير صالح", "red");
        return false;
    }

    // التحقق من قوة كلمة المرور
    if (password.length < 6) {
        showMessage("كلمة المرور يجب أن تكون 6 أحرف على الأقل", "red");
        return false;
    }

    // التحقق من تطابق كلمتي المرور
    if (password !== confirmPassword) {
        showMessage("كلمتا المرور غير متطابقتين", "red");
        return false;
    }

    return true;
}

// دالة للتحقق من وجود البريد الإلكتروني مسبقاً
function isEmailRegistered(email, users) {
    return users.some(user => user.email === email);
}

// دالة لعرض الرسائل للمستخدم
function showMessage(message, color) {
    const messageElement = document.getElementById("message");
    messageElement.style.color = color;
    messageElement.innerText = message;
}

// دالة لتوليد معرّف فريد للمستخدم
function generateUserId() {
    return Date.now().toString();
}

// دالة للتشفير البسيط (لا تستخدم هذا في بيئة إنتاجية)
function simpleEncrypt(text) {
    return btoa(text); // تشفير base64 (ليس آمناً للاستخدام الفعلي)
}
const newUser = {
    id: generateUserId(),
    username: "اسم المستخدم",
    email: "email@example.com",
    password: "كلمة المرور المشفرة",
    createdAt: "تاريخ الإنشاء"
};
let users = JSON.parse(localStorage.getItem("users")) || [];
users.push(newUser);
localStorage.setItem("users", JSON.stringify(users));
// في دالة register()
let users = JSON.parse(localStorage.getItem("users")) || [];
users.push(newUser);
localStorage.setItem("users", JSON.stringify(users));