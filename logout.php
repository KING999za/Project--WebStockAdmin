<?php
session_start();
include 'log_function.php';

// ตรวจสอบว่ามี session อยู่ก่อน logout
if (isset($_SESSION['user_id'], $_SESSION['name'])) {
    $conn = new mysqli('localhost', 'root', '', 'users_db');
    if ($conn->connect_error) {
        die("เชื่อมต่อฐานข้อมูลล้มเหลว: " . $conn->connect_error);
    }

    $user_id = $_SESSION['user_id'];
    $user_name = $_SESSION['name']; // 👈 เพิ่มการเก็บชื่อ

    $action = 'logout';
    $description = "ผู้ใช้ $user_name ออกจากระบบ";

    // ✅ เก็บ log
    write_log($conn, $user_id, $action, $description);

    $conn->close();
}

// ลบ session แล้วกลับไปหน้า login
session_unset();
session_destroy();
header("Location: index.php");
exit();
?>
