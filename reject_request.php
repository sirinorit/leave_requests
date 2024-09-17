<?php
session_start();
require 'db_connect.php'; // เรียกใช้การเชื่อมต่อฐานข้อมูล

// ตรวจสอบว่ามีข้อมูล POST หรือไม่
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['leave_request_id'])) {
    $leave_request_id = $_POST['leave_request_id'];

    // อัปเดตสถานะคำขอในฐานข้อมูล
    $sql = "UPDATE leave_requests SET status = 'rejected' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $leave_request_id);

    if ($stmt->execute()) {
        // ถ้าการอัปเดตสำเร็จ
        echo "<p>คำขอลาถูกปฏิเสธเรียบร้อยแล้ว</p>";
    } else {
        // ถ้ามีข้อผิดพลาด
        echo "<p>ข้อผิดพลาดในการปฏิเสธคำขอ: " . $conn->error . "</p>";
    }

    // ปิดการเชื่อมต่อฐานข้อมูล
    $stmt->close();
    $conn->close();

    // กลับไปที่หน้า approval page
    header("Location: approval_page.php");
    exit();
} else {
    // ถ้าไม่มีข้อมูล POST หรือ POST ไม่ถูกต้อง
    echo "<p>ข้อมูลไม่ถูกต้อง</p>";
}
?>
