<?php
$conn = new mysqli('localhost', 'root', '', 'users_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$name = $_POST['name'];
$description = $_POST['description'] ?? '';
$price = $_POST['price'];
$stock_quantity = $_POST['stock_quantity'] ?? 0;
$category_id = $_POST['category_id']; // <-- แก้ตรงนี้
$image_url = $_POST['image_url'] ?? '';
$created_at = date('Y-m-d H:i:s');
$updated_at = date('Y-m-d H:i:s');

// ตรวจสอบให้แน่ใจว่า fields เหล่านี้มีในตาราง products
$sql = "INSERT INTO products (name, description, price, stock_quantity, created_at, updated_at, category_id, image_url)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssdissis", $name, $description, $price, $stock_quantity, $created_at, $updated_at, $category_id, $image_url);

if ($stmt->execute()) {
    echo "เพิ่มสินค้าเรียบร้อย";
} else {
    echo "เกิดข้อผิดพลาด: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
