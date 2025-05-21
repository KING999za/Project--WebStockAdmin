<?php
session_start();

include 'connect.php';
include 'log_function.php';

// สมมุติว่า login สำเร็จแล้ว
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // ตรวจสอบข้อมูลผู้ใช้จากฐานข้อมูล (ตัวอย่างการตรวจสอบ)
    $stmt = $conn->prepare("SELECT id, email FROM users WHERE email = ? AND password = ?");
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // ถ้าพบผู้ใช้
        $stmt->bind_result($user_id, $db_email);
        $stmt->fetch();

        $_SESSION['user_id'] = $user_id;
        $_SESSION['email'] = $db_email;

        // เขียน log การเข้าสู่ระบบ
        write_log($conn, $user_id, 'login', 'Login successful: ' . $db_email);

    
        header('Location: user_page.php');
        exit();
    } else {
        $_SESSION['login_error'] = 'ข้อมูลการเข้าสู่ระบบไม่ถูกต้อง';
    }
}

// ตัวแปร error (ไม่จำเป็นต้องล้าง session ทั้งหมด)
$errors = [
    'login' => $_SESSION['login_error'] ?? '',
    'register' => $_SESSION['register_error'] ?? '',
    'forgot-password' => $_SESSION['forgot_password_error'] ?? ''
];

// ตัวแปรควบคุมฟอร์ม
$activeForm = $_SESSION['active_form'] ?? 'login';

// ฟังก์ชันแสดงข้อผิดพลาด
function showError($error)
{
    return !empty($error) ? "<p class='error-message'>$error</p>" : '';
}

function isActiveForm($formName, $activeForm)
{
    return $formName === $activeForm ? 'active' : '';
}

// ล้างค่าภายหลังการแสดงข้อผิดพลาด
unset($_SESSION['login_error'], $_SESSION['register_error'], $_SESSION['forgot_password_error']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fullstack</title>

    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <!-- Login Form -->
    <div class="form-box <?= isActiveForm('login', $activeForm); ?>" id="login-form">
        <form action="login_register.php" method="post">
            <h2>ล็อกอิน</h2>
            <img src="./images/imag2.png" alt="" class="logo-img">
            <?= showError($errors['login']); ?>
            <input type="email" name="email" placeholder="อีเมล" required>
            <input type="password" name="password" placeholder="รหัสผ่าน" required>
            <button type="submit" name="login">เข้าสู่ระบบ</button>
            <p>ยังไม่มีบัญชีใช่ไหม?<a href="#" onclick="showForm('register-form')">ลงทะเบียน</a></p>
            <p>รีเซ็ตรหัสผ่านของคุณ<a href="#" onclick="showForm('forgot-password-form')">ลืมรหัสผ่าน</a></p>
        </form>
    </div>

    <!-- Register Form -->
    <div class="form-box <?= isActiveForm('register', $activeForm); ?>" id="register-form">
        <form action="login_register.php" method="post">
            <h2>ลงทะเบียน</h2>
            <?= showError($errors['register']); ?>
            <input type="text" name="name" placeholder="ชื่อ" required>
            <input type="email" name="email" placeholder="อีเมล" required>
            <input type="password" name="password" placeholder="รหัสผ่าน" required>
            <select name="role" required>
                <option value="">--เลือก--</option>
                <option value="user">ผู้ใช้</option>
                <option value="admin">แอดมิน</option>
            </select>
            <button type="submit" name="register">ลงทะเบียน</button>
            <p>มีบัญชีอยู่แล้ว?<a href="#" onclick="showForm('login-form')">เข้าสู่ระบบ</a></p>
        </form>
    </div>

    <!-- Forgot Password Form -->
    <div class="form-box <?= isActiveForm('forgot-password', $activeForm); ?>" id="forgot-password-form">
        <form action="login_register.php" method="post">
            <h2>ลืมรหัสผ่าน</h2>
            <?= showError($errors['forgot-password']); ?>
            <input type="email" name="email" placeholder="อีเมล" required>
            <button type="submit" name="forgot-password">รีเซ็ตรหัสผ่าน</button>
            <p>มีบัญชีอยู่แล้ว?<a href="#" onclick="showForm('login-form')">เข้าสู่ระบบ</a></p>
        </form>
    </div>
</div>

<script src="script.js"></script>

</body>
</html>
