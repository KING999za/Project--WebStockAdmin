<?php
session_start();

$conn = new mysqli('localhost', 'root', '', 'users_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

include 'log_function.php';

$name = $_POST['name'];
$description = $_POST['description'] ?? '';
$price = $_POST['price'];
$stock_quantity = $_POST['stock_quantity'] ?? 0;
$category_id = $_POST['category_id'];
$image_url = $_POST['image_url'] ?? '';
$created_at = date('Y-m-d H:i:s');
$updated_at = date('Y-m-d H:i:s');

$sql = "INSERT INTO products (name, description, price, stock_quantity, created_at, updated_at, category_id, image_url)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssdissis", $name, $description, $price, $stock_quantity, $created_at, $updated_at, $category_id, $image_url);

if ($stmt->execute()) {
    echo "เพิ่มสินค้าเรียบร้อย";

    // ✅ เพิ่มบันทึก log
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $action = 'add_product';
        $new_product_id = $stmt->insert_id;
        $description_log = "เพิ่มสินค้า: รหัส $new_product_id จำนวน $stock_quantity";
        write_log($conn, $user_id, $action, $description_log);
    }

} else {
    echo "เกิดข้อผิดพลาด: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
