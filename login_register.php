<?php
session_start();

// เก็บค่าผิดพลาดที่มีใน session
$errors = [
    'login' => $_SESSION['login_error'] ?? '',
    'register' => $_SESSION['register_error'] ?? ''
];

// กำหนดฟอร์มที่แอคทีฟอยู่
$activeForm = $_SESSION['active_form'] ?? 'login';

// หลังจากทำงานเสร็จในหน้าแล้ว ให้ล้าง session ที่ไม่จำเป็น
session_unset();

// ฟังก์ชันแสดงข้อผิดพลาด
function showError($error)
{
    return !empty($error) ? "<p class='error-message'>$error</p>" : '';
}

// ฟังก์ชันตรวจสอบฟอร์มที่กำลังแอคทีฟ
function isActiveForm($formName, $activeForm)
{
    return $formName === $activeForm ? 'active' : '';
}

if (isset($_POST['register'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    // เช็กว่าอีเมลนี้มีอยู่แล้วไหม
    $conn = new mysqli('localhost', 'root', '', 'users_db'); // เปลี่ยนเป็น 'users_db'
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // ตรวจสอบอีเมลว่ามีในระบบหรือไม่
    $stmt = $conn->prepare("SELECT email FROM users WHERE email = ?");
    $stmt->bind_param("s", $email); // ป้องกัน SQL Injection
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $_SESSION['register_error'] = 'Email is already registered!';
        $_SESSION['active_form'] = 'register';
    } else {
        // สมัครใหม่
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $password, $role);
        $stmt->execute();
        $_SESSION['register_error'] = 'Registration successful!';
        $_SESSION['active_form'] = 'login';
    }

    $stmt->close();
    $conn->close();

    header("Location: index.php");
    exit();
}

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // ตรวจสอบอีเมลและรหัสผ่าน
    $conn = new mysqli('localhost', 'root', '', 'users_db'); // เปลี่ยนเป็น 'users_db'
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email); // ป้องกัน SQL Injection
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            // ตั้งค่าข้อมูลใน session
            $_SESSION['user_id'] = $user['id']; // เพิ่ม user_id ใน session
            $_SESSION['name'] = $user['name'];
            $_SESSION['email'] = $user['email'];

            // เขียน log สำหรับการเข้าสู่ระบบ
            write_log($conn, $user['id'], 'login', 'Login successful');

            // ส่งผู้ใช้ไปที่หน้าต่างๆ
            if ($user['role'] === 'admin') {
                header("Location: admin_page.php");
            } else {
                header("Location: user_page.php");
            }
            exit();
        }
    }

    // ถ้า login ไม่สำเร็จ
    $_SESSION['login_error'] = 'อีเมลหรือรหัสผ่านไม่ถูกต้อง';
    $_SESSION['active_form'] = 'login';
    header("Location: index.php");
    exit();
}
function write_log($conn, $user_id, $action, $description) {
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'UNKNOWN';

    // เตรียมการ query สำหรับบันทึก log
    $stmt = $conn->prepare("INSERT INTO logs (user_id, action, description, ip_address, user_agent) VALUES (?, ?, ?, ?, ?)");
    if ($stmt) {
        // เชื่อมโยงพารามิเตอร์และทำการ execute
        $stmt->bind_param("issss", $user_id, $action, $description, $ip_address, $user_agent);
        $stmt->execute();
        $stmt->close();
    } else {
        // ถ้ามีข้อผิดพลาดจะเขียนข้อความลงใน error_log
        error_log("Log Error: " . $conn->error);
    }
}
?>
