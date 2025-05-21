<?php
session_start();

// เชื่อมต่อฐานข้อมูลเพื่อบันทึก log
$conn = new mysqli('localhost', 'root', '', 'users_db'); // เชื่อมต่อฐานข้อมูล

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ตรวจสอบว่าผู้ใช้ล็อกอินอยู่หรือไม่
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id']; // เก็บ user_id เพื่อบันทึก log

    // บันทึกการออกจากระบบ (logout) ใน log
    $stmt = $conn->prepare("INSERT INTO logs (user_id, action, description) VALUES (?, ?, ?)");
    $action = 'logout';
    $description = 'Logout';
    $stmt->bind_param("iss", $user_id, $action, $description); // bind param สำหรับการป้องกัน SQL Injection
    $stmt->execute();
    $stmt->close();
}

// ลบข้อมูล session ที่ไม่จำเป็น
unset($_SESSION['user_id']);
unset($_SESSION['name']);
unset($_SESSION['email']);
unset($_SESSION['role']); // ถ้าคุณใช้ role ใน session

// ล้างทั้งหมดหรือบางส่วนตามต้องการ
session_destroy();

// ส่งผู้ใช้กลับไปที่หน้า index.php
header("Location: index.php");
exit();
?>
