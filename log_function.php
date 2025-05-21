<?php
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
