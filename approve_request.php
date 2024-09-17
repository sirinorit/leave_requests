<?php
session_start();
require 'db_connect.php'; // เชื่อมต่อฐานข้อมูล

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $leave_request_id = $_POST['leave_request_id'];
    $approval_status = $_POST['approval_status'];
    $approver_username = $_SESSION['username']; // ใช้ username ของผู้อนุมัติจาก session
    $approval_level = $_POST['approval_level']; // ระบุระดับการอนุมัติที่กำลังดำเนินการอยู่

    // อัปเดตสถานะคำขอตามระดับการอนุมัติ
    if ($approval_level == 1) {
        $sql = "UPDATE leave_requests SET approval_status_1 = ? WHERE id = ?";
    } elseif ($approval_level == 2) {
        $sql = "UPDATE leave_requests SET approval_status_2 = ? WHERE id = ?";
    } elseif ($approval_level == 3) {
        $sql = "UPDATE leave_requests SET approval_status_3 = ? WHERE id = ?";
    } elseif ($approval_level == 4) {
        $sql = "UPDATE leave_requests SET approval_status_4 = ? WHERE id = ?";
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $approval_status, $leave_request_id);

    if ($stmt->execute()) {
        // บันทึกข้อมูลการอนุมัติ
        $sql = "INSERT INTO approvals (leave_request_id, approver_username, approval_status, approval_level) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issi", $leave_request_id, $approver_username, $approval_status, $approval_level);
        $stmt->execute();

        echo "Request " . $approval_status;
    } else {
        echo "Error updating request: " . $conn->error;
    }
    
    // ปิดการเชื่อมต่อฐานข้อมูล
    $stmt->close();
    $conn->close();

    // เปลี่ยนไปที่หน้า approval page
    header("Location: approval_page.php");
    exit();
} else {
    echo "<p>Invalid request method</p>";
}
?>
