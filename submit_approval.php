<?php
include 'db_connect.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $leave_request_id = $_POST['leave_request_id'];
    $approval_level = $_POST['approval_level'];
    $approval_status = $_POST['approval_status'];
    $user_role = $_SESSION['user_role'];

    // ตรวจสอบบทบาทผู้ใช้เพื่อให้แน่ใจว่าการอนุมัติเป็นไปตามลำดับ
    if ($approval_level === '1' && $user_role === 'Teacher') {
        $sql = "UPDATE leave_requests SET approval_level_1 = ? WHERE id = ?";
    } elseif ($approval_level === '2' && $user_role === 'Head') {
        $sql = "UPDATE leave_requests SET approval_level_2 = ? WHERE id = ?";
    } elseif ($approval_level === '3' && $user_role === 'Director_dom') {
        $sql = "UPDATE leave_requests SET approval_level_3 = ? WHERE id = ?";
    } elseif ($approval_level === '4' && $user_role === 'Director_aca') {
        $sql = "UPDATE leave_requests SET approval_level_4 = ? WHERE id = ?";
    } else {
        die("Unauthorized action.");
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('si', $approval_status, $leave_request_id);
    if ($stmt->execute()) {
        echo "Approval status updated successfully.";
    } else {
        echo "Error updating status: " . $conn->error;
    }

    $stmt->close();
}
$conn->close();
?>
