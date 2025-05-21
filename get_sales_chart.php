<?php
// เชื่อมต่อฐานข้อมูล
$conn = new mysqli('localhost', 'root', '', 'users_db');
if ($conn->connect_error) {
    die(json_encode(["error" => "ไม่สามารถเชื่อมต่อฐานข้อมูลได้: " . $conn->connect_error]));
}

// เตรียม array เดือนภาษาไทย
$months = ['JAN.', 'FEB.', 'MAR.', 'APR.', 'MAY.', 'JUN.', 'JUL.', 'AUG.', 'SEP.', 'OCT.', 'NOV.', 'DEC.'];
$monthly_data = array_fill(0, 12, 0); // ตั้งค่าทุกเดือนเป็น 0

// Query ยอดขายรายเดือน
$stmt = $conn->prepare("
    SELECT MONTH(sale_date) AS sale_month, SUM(quantity * price) AS total 
    FROM sales 
    GROUP BY MONTH(sale_date)
    ORDER BY MONTH(sale_date)
");
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $index = (int)$row['sale_month'] - 1;
    $monthly_data[$index] = (float)$row['total'];
}
$stmt->close();
$conn->close();

// ส่งข้อมูลกลับในรูปแบบ JSON
echo json_encode([
    "labels" => $months,
    "values" => $monthly_data
]);
?>
