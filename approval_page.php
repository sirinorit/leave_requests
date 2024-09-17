<?php
include 'db_connect.php';
session_start();
$user_role = strtolower($_SESSION['user_role']); // ตรวจสอบบทบาทของผู้ใช้และเปลี่ยนเป็นตัวพิมพ์เล็ก

// ตรวจสอบระดับผู้อนุมัติที่ต่ำกว่าตามบทบาทของผู้ใช้
$approval_condition = '';
switch ($user_role) {
    case 'teacher':
        $approval_condition = "approval_level_1 IS NULL";
        break;
    case 'head':
        $approval_condition = "(approval_level_1 IS NOT NULL AND approval_level_2 IS NULL)";
        break;
    case 'director_dom':
        $approval_condition = "(approval_level_1 IS NOT NULL AND approval_level_2 IS NOT NULL AND approval_level_3 IS NULL)";
        break;
    case 'director_aca':
        $approval_condition = "(approval_level_1 IS NOT NULL AND approval_level_2 IS NOT NULL AND approval_level_3 IS NOT NULL AND approval_level_4 IS NULL)";
        break;
    default:
        $approval_condition = "1=0"; // ไม่มีคำขอที่ต้องการการอนุมัติ
}

// ดึงคำขอที่ต้องการการอนุมัติตามบทบาทของผู้ใช้
$sql = "SELECT * FROM leave_requests WHERE $approval_condition";
$result = $conn->query($sql);

if (!$result) {
    die("Database query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approval Page</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Leave Request Approval</h1>
    </header>
    <div class="container">
        <h2>Pending Leave Requests</h2>
        <a href="download_leave_requests_excel.php" class="button">ดาวน์โหลดฟอร์มขอลา (Excel)</a>
        <a href="download_leave_requests_pdf.php" class="button">ดาวน์โหลดฟอร์มขอลา (PDF)</a>
        <table>
            <thead>
                <tr>
                    <th>Username</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Reason</th>
                    <th>Approval Level 1 (Teacher)</th>
                    <th>Approval Level 2 (Head)</th>
                    <th>Approval Level 3 (Director_dom)</th>
                    <th>Approval Level 4 (Director_aca)</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['first_name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['last_name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['start_date']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['end_date']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['reason']) . "</td>";
                        echo "<td>" . ($row["approval_level_1"] ? "Approved" : "Pending") . "</td>";
                        echo "<td>" . ($row["approval_level_2"] ? "Approved" : "Pending") . "</td>";
                        echo "<td>" . ($row["approval_level_3"] ? "Approved" : "Pending") . "</td>";
                        echo "<td>" . ($row["approval_level_4"] ? "Approved" : "Pending") . "</td>";
                        echo "<td>";

                        // ตรวจสอบระดับผู้ใช้งานและสถานะการอนุมัติ
                        if ($row["approval_level_1"] === NULL && $user_role === 'teacher') {
                            echo "<form method='POST' action='submit_approval.php'>";
                            echo "<input type='hidden' name='leave_request_id' value='" . $row["id"] . "'>";
                            echo "<input type='hidden' name='approval_level' value='1'>";
                            echo "<button type='submit' name='approval_status' value='approved'>Approve (Level 1)</button>";
                            echo "<button type='submit' name='approval_status' value='rejected'>Reject (Level 1)</button>";
                            echo "</form><br>";
                        } elseif ($row["approval_level_2"] === NULL && $user_role === 'head') {
                            echo "<form method='POST' action='submit_approval.php'>";
                            echo "<input type='hidden' name='leave_request_id' value='" . $row["id"] . "'>";
                            echo "<input type='hidden' name='approval_level' value='2'>";
                            echo "<button type='submit' name='approval_status' value='approved'>Approve (Level 2)</button>";
                            echo "<button type='submit' name='approval_status' value='rejected'>Reject (Level 2)</button>";
                            echo "</form><br>";
                        } elseif ($row["approval_level_3"] === NULL && $user_role === 'director_dom') {
                            echo "<form method='POST' action='submit_approval.php'>";
                            echo "<input type='hidden' name='leave_request_id' value='" . $row["id"] . "'>";
                            echo "<input type='hidden' name='approval_level' value='3'>";
                            echo "<button type='submit' name='approval_status' value='approved'>Approve (Level 3)</button>";
                            echo "<button type='submit' name='approval_status' value='rejected'>Reject (Level 3)</button>";
                            echo "</form><br>";
                        } elseif ($row["approval_level_4"] === NULL && $user_role === 'director_aca') {
                            echo "<form method='POST' action='submit_approval.php'>";
                            echo "<input type='hidden' name='leave_request_id' value='" . $row["id"] . "'>";
                            echo "<input type='hidden' name='approval_level' value='4'>";
                            echo "<button type='submit' name='approval_status' value='approved'>Approve (Level 4)</button>";
                            echo "<button type='submit' name='approval_status' value='rejected'>Reject (Level 4)</button>";
                            echo "</form><br>";
                        }

                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='11'>No pending requests</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    <footer>
        <p>&copy; 2024 Your Company</p>
    </footer>
</body>
</html>

<?php
// ปิดการเชื่อมต่อฐานข้อมูล
$conn->close();
?>
