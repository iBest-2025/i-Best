// ========== نظام إدارة المستخدمين ==========

// دالة تسجيل الدخول
function login() {
  const username = document.getElementById("username").value.trim();
  const password = document.getElementById("password").value;
  const messageEl = document.getElementById("message");

  // التحقق من إدخال البيانات
  if (!username || !password) {
    showMessage("الرجاء إدخال اسم المستخدم وكلمة المرور", "error");
    return;
  }

  // جلب المستخدمين المسجلين من localStorage
  const users = JSON.parse(localStorage.getItem("users")) || [];
  
  // البحث عن المستخدم
  const user = users.find(u => u.username === username && u.password === password);
  
  if (user) {
    // حفظ حالة تسجيل الدخول
    localStorage.setItem("currentUser", JSON.stringify({
      id: user.id,
      username: user.username,
      name: user.name
    }));
    
    showMessage("تم تسجيل الدخول بنجاح!", "success");
    
    // توجيه المستخدم للصفحة الرئيسية بعد 1 ثانية
    setTimeout(() => {
      window.location.href = "home.html";
    }, 1000);
  } else {
    showMessage("اسم المستخدم أو كلمة المرور غير صحيحة", "error");
  }
}

// دالة تسجيل مستخدم جديد
function register() {
  const name = document.getElementById("regName").value.trim();
  const username = document.getElementById("regUsername").value.trim();
  const password = document.getElementById("regPassword").value;
  const confirmPassword = document.getElementById("regConfirmPassword").value;

  // التحقق من البيانات
  if (!name || !username || !password) {
    showMessage("الرجاء ملء جميع الحقول", "error");
    return;
  }

  if (password !== confirmPassword) {
    showMessage("كلمة المرور غير متطابقة", "error");
    return;
  }

  if (password.length < 6) {
    showMessage("كلمة المرور يجب أن تكون 6 أحرف على الأقل", "error");
    return;
  }

  // جلب المستخدمين الحاليين
  const users = JSON.parse(localStorage.getItem("users")) || [];

  // التحقق من عدم وجود مستخدم بنفس الاسم
  if (users.some(u => u.username === username)) {
    showMessage("اسم المستخدم موجود مسبقاً", "error");
    return;
  }

  // إنشاء مستخدم جديد
  const newUser = {
    id: Date.now().toString(),
    name,
    username,
    password, // في تطبيق حقيقي يجب تشفير كلمة المرور
    joinDate: new Date().toISOString()
  };

  // حفظ المستخدم الجديد
  users.push(newUser);
  localStorage.setItem("users", JSON.stringify(users));
  
  showMessage("تم إنشاء الحساب بنجاح، يمكنك تسجيل الدخول الآن", "success");
  
  // إعادة تعيين الحقول
  document.getElementById("regName").value = "";
  document.getElementById("regUsername").value = "";
  document.getElementById("regPassword").value = "";
  document.getElementById("regConfirmPassword").value = "";
}

// دالة تسجيل الخروج
function logout() {
  if (confirm('هل أنت متأكد أنك تريد تسجيل الخروج؟')) {
    localStorage.removeItem("currentUser");
    window.location.href = "login.html";
  }
}

// ========== نظام المنشورات ==========

let postImageData = null;

// دالة لتحميل صورة المنشور
function handleImageUpload(e) {
  const file = e.target.files[0];
  const preview = document.getElementById("imagePreview");
  
  if (!file) return;
  
  // التحقق من حجم الصورة (2MB كحد أقصى)
  if (file.size > 2 * 1024 * 1024) {
    showMessage("حجم الصورة يجب أن يكون أقل من 2MB", "error");
    e.target.value = "";
    return;
  }

  // التحقق من نوع الملف
  const validTypes = ["image/jpeg", "image/png", "image/gif"];
  if (!validTypes.includes(file.type)) {
    showMessage("نوع الملف غير مدعوم. الرجاء اختيار صورة (JPEG, PNG, GIF)", "error");
    e.target.value = "";
    return;
  }

  const reader = new FileReader();
  reader.onload = function(event) {
    postImageData = event.target.result;
    preview.innerHTML = `<img src="${postImageData}" class="preview-image">`;
    preview.style.display = "block";
  }
  reader.readAsDataURL(file);
}

// دالة نشر المنشور
function publishPost() {
  const currentUser = JSON.parse(localStorage.getItem("currentUser"));
  
  if (!currentUser) {
    showMessage("الرجاء تسجيل الدخول أولاً", "error");
    return;
  }

  const postText = document.getElementById("postInput").value.trim();
  
  if (!postText && !postImageData) {
    showMessage("الرجاء كتابة نص أو إضافة صورة", "error");
    return;
  }

  // إنشاء المنشور الجديد
  const newPost = {
    id: Date.now().toString(),
    author: {
      id: currentUser.id,
      name: currentUser.name,
      username: currentUser.username
    },
    text: postText,
    image: postImageData || null,
    createdAt: new Date().toISOString(),
    likes: [],
    comments: []
  };

  // حفظ المنشور
  const posts = JSON.parse(localStorage.getItem("posts")) || [];
  posts.unshift(newPost);
  localStorage.setItem("posts", JSON.stringify(posts));

  // إعادة تعيين الحقول
  document.getElementById("postInput").value = "";
  document.getElementById("postImageInput").value = "";
  document.getElementById("imagePreview").style.display = "none";
  postImageData = null;
  
  showMessage("تم نشر المنشور بنجاح!", "success");
  
  // إعادة تحميل المنشورات إذا كانت الصفحة تحتوي عليها
  if (typeof loadPosts === "function") {
    loadPosts();
  }
}

// دالة عرض الرسائل
function showMessage(msg, type = "success") {
  const messageEl = document.getElementById("message");
  messageEl.textContent = msg;
  messageEl.className = `message ${type}`;
  messageEl.style.display = "block";

  // إخفاء الرسالة بعد 5 ثواني
  setTimeout(() => {
    messageEl.style.display = "none";
  }, 5000);
}

// ========== تهيئة الأحداث ==========

// تهيئة الأحداث عند تحميل الصفحة
document.addEventListener("DOMContentLoaded", function() {
  // التحقق من تسجيل الدخول
  const currentUser = JSON.parse(localStorage.getItem("currentUser"));
  
  // إذا كان المستخدم مسجل الدخول
  if (currentUser) {
    // عرض اسم المستخدم في الصفحة
    const userElements = document.querySelectorAll(".user-name");
    userElements.forEach(el => {
      el.textContent = currentUser.name;
    });
    
    // إظهار/إخفاء عناصر حسب حالة تسجيل الدخول
    const authElements = document.querySelectorAll(".auth-only");
    const guestElements = document.querySelectorAll(".guest-only");
    
    authElements.forEach(el => el.style.display = "block");
    guestElements.forEach(el => el.style.display = "none");
  }
  
  // تهيئة حدث زر تسجيل الخروج
  const logoutBtn = document.getElementById("logoutBtn");
  if (logoutBtn) {
    logoutBtn.addEventListener("click", logout);
  }
  
  // تهيئة حدث تحميل الصور
  const postImageInput = document.getElementById("postImageInput");
  if (postImageInput) {
    postImageInput.addEventListener("change", handleImageUpload);
  }
  
  // تهيئة حدث زر النشر
  const publishBtn = document.getElementById("publishBtn");
  if (publishBtn) {
    publishBtn.addEventListener("click", publishPost);
  }
  
  // تهيئة نموذج تسجيل الدخول
  const loginForm = document.getElementById("loginForm");
  if (loginForm) {
    loginForm.addEventListener("submit", function(e) {
      e.preventDefault();
      login();
    });
  }
  
  // تهيئة نموذج التسجيل
  const registerForm = document.getElementById("registerForm");
  if (registerForm) {
    registerForm.addEventListener("submit", function(e) {
      e.preventDefault();
      register();
    });
  }
});
// models/userModel.js
const pool = require('../db');
const bcrypt = require('bcryptjs');

class User {
  static async create(username, email, password) {
    const hashedPassword = await bcrypt.hash(password, 10);
    const [result] = await pool.execute(
      'INSERT INTO users (username, email, password) VALUES (?, ?, ?)',
      [username, email, hashedPassword]
    );
    return result.insertId;
  }

  static async findByEmail(email) {
    const [rows] = await pool.execute('SELECT * FROM users WHERE email = ?', [email]);
    return rows[0];
  }
}

module.exports = User;
// routes/authRoutes.js
const express = require('express');
const User = require('../models/userModel');
const jwt = require('jsonwebtoken');
const router = express.Router();

// تسجيل مستخدم جديد
router.post('/register', async (req, res) => {
  try {
    const { username, email, password } = req.body;
    const userId = await User.create(username, email, password);
    res.status(201).json({ message: 'User created', userId });
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
});

// تسجيل الدخول
router.post('/login', async (req, res) => {
  try {
    const { email, password } = req.body;
    const user = await User.findByEmail(email);
    if (!user) return res.status(404).json({ error: 'User not found' });

    const isMatch = await bcrypt.compare(password, user.password);
    if (!isMatch) return res.status(401).json({ error: 'Invalid credentials' });

    const token = jwt.sign({ id: user.id }, 'your-secret-key', { expiresIn: '1h' });
    res.json({ token });
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
});

module.exports = router;
// app.js
const express = require('express');
const cors = require('cors');
const authRoutes = require('./routes/authRoutes');

const app = express();
app.use(cors());
app.use(express.json());

app.use('/api/auth', authRoutes);

const PORT = 3000;
app.listen(PORT, () => {
  console.log(`Server running on http://localhost:${PORT}`);
});
// مثال بتسجيل الدخول في React
import React, { useState } from 'react';
import axios from 'axios';

function Login() {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');

  const handleLogin = async () => {
    try {
      const response = await axios.post('http://localhost:3000/api/auth/login', {
        email,
        password
      });
      localStorage.setItem('token', response.data.token);
      alert('Login successful!');
    } catch (error) {
      alert('Login failed!');
    }
  };

  return (
    <div>
      <input type="email" value={email} onChange={(e) => setEmail(e.target.value)} />
      <input type="password" value={password} onChange={(e) => setPassword(e.target.value)} />
      <button onClick={handleLogin}>Login</button>
    </div>
  );
}
const { Pool } = require('pg');
const pool = new Pool({
  user: 'postgres',
  host: 'localhost',
  database: 'user_authentication',
  password: 'yourpassword',
  port: 5432,
});