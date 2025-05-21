<?php
// เชื่อมต่อฐานข้อมูล
$conn = new mysqli('localhost', 'root', '', 'users_db');

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// รับค่าจากฟอร์มและตรวจสอบความถูกต้อง
$product_id = $_POST['product_id'] ?? null;
$quantity = $_POST['quantity'] ?? null;

if ($product_id === null || $quantity === null || !is_numeric($product_id) || !is_numeric($quantity)) {
    echo "❌ กรุณากรอกข้อมูลให้ถูกต้อง";
    exit();
}

// ตรวจสอบว่าในสต็อกมีเพียงพอหรือไม่
$sql_check_stock = "SELECT stock_quantity FROM products WHERE id = ?";
$stmt = $conn->prepare($sql_check_stock);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    if ($row['stock_quantity'] >= $quantity) {
        // ลดจำนวนสินค้าในสต็อก
        $new_stock_quantity = $row['stock_quantity'] - $quantity;
        $sql_update_stock = "UPDATE products SET stock_quantity = ? WHERE id = ?";
        $update_stmt = $conn->prepare($sql_update_stock);
        $update_stmt->bind_param("ii", $new_stock_quantity, $product_id);
        $update_stmt->execute();

        // บันทึกการขาย
        $price_sql = "SELECT price FROM products WHERE id = ?";
        $price_stmt = $conn->prepare($price_sql);
        $price_stmt->bind_param("i", $product_id);
        $price_stmt->execute();
        $price_result = $price_stmt->get_result();
        $price_row = $price_result->fetch_assoc();
        $price = $price_row['price'];

        $sql_insert_sale = "INSERT INTO sales (product_id, quantity, price) VALUES (?, ?, ?)";
        $insert_stmt = $conn->prepare($sql_insert_sale);
        $insert_stmt->bind_param("iid", $product_id, $quantity, $price);
        $insert_stmt->execute();

        echo "✅ ขายสินค้าเรียบร้อย!";
    } else {
        echo "❌ จำนวนสินค้าคงเหลือไม่เพียงพอ!";
    }
} else {
    echo "❌ ไม่พบสินค้าดังกล่าวในฐานข้อมูล!";
}

// ปิดการเชื่อมต่อ
$conn->close();
?>
