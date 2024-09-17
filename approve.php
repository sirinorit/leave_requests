<?php
include 'db_connect.php';

$request_id = $_GET['request_id'];
$level = $_GET['level'];

if ($level == 1) {
    $sql = "UPDATE leave_requests SET approval_level_1 = 'approved' WHERE id = $request_id";
} elseif ($level == 2) {
    $sql = "UPDATE leave_requests SET approval_level_2 = 'approved' WHERE id = $request_id";
} elseif ($level == 3) {
    $sql = "UPDATE leave_requests SET approval_level_3 = 'approved' WHERE id = $request_id";
} elseif ($level == 4) {
    $sql = "UPDATE leave_requests SET approval_level_4 = 'approved' WHERE id = $request_id";
}

if ($conn->query($sql) === TRUE) {
    echo "Leave request approved successfully.";
} else {
    echo "Error updating record: " . $conn->error;
}

$conn->close();
?>
