<?php
// เชื่อมต่อฐานข้อมูล
$conn = new mysqli('localhost', 'root', '', 'users_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// รับค่าชื่อหมวดหมู่จากฟอร์ม
$categoryName = $_POST['categoryName'];

// ตรวจสอบว่าชื่อหมวดหมู่มีอยู่แล้วหรือไม่
$sqlCheck = "SELECT * FROM categories WHERE name = ?";
$stmt = $conn->prepare($sqlCheck);
$stmt->bind_param("s", $categoryName);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo "หมวดหมู่นี้มีอยู่แล้วในระบบ";
} else {
    // เพิ่มหมวดหมู่ใหม่
    $sqlInsert = "INSERT INTO categories (name) VALUES (?)";
    $stmt = $conn->prepare($sqlInsert);
    $stmt->bind_param("s", $categoryName);

    if ($stmt->execute()) {
        echo "เพิ่มหมวดหมู่ใหม่เรียบร้อย";
    } else {
        echo "เกิดข้อผิดพลาดในการเพิ่มหมวดหมู่: " . $stmt->error;
    }
}

$conn->close();
?>
