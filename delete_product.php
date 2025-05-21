<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'users_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

include 'log_function.php';

$user_id = $_SESSION['user_id'];
$product_id = $_POST['product_id'] ?? null;

if ($product_id) {
    // ตรวจสอบก่อนว่ามีสินค้าอยู่จริง
    $stmt = $conn->prepare("SELECT name FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // ลบสินค้า
        $delete = $conn->prepare("DELETE FROM products WHERE id = ?");
        $delete->bind_param("i", $product_id);
        $delete->execute();

        // เก็บ log
        $product = $result->fetch_assoc();
        $description = "ลบสินค้า: รหัส $product_id, ชื่อ {$product['name']}";
        write_log($conn, $user_id, 'delete_product', $description);

        header("Location: DataProduct.php?status=deleted");
        exit();
    } else {
        echo "ไม่พบสินค้าที่ต้องการลบ";
    }
} else {
    echo "กรุณาระบุรหัสสินค้า";
}
?>
