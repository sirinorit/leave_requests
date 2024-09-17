<?php
include 'db_connect.php';
session_start();
$username = $_SESSION['username'];

// ตรวจสอบว่ามีการ logout หรือไม่
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}

// ดึงข้อมูลคำขอและข้อมูลผู้ใช้จากตาราง leave_requests และ users
$sql = "SELECT lr.*, u.prefix, u.first_name, u.last_name, u.class 
        FROM leave_requests lr
        JOIN users u ON lr.username = u.username
        WHERE lr.username = '$username'";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard นักเรียน</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2ecc71;
            --background-color: #ecf0f1;
            --text-color: #34495e;
            --header-color: #2c3e50;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Kanit', sans-serif;
            background-color: var(--background-color);
            color: var(--text-color);
            line-height: 1.6;
        }
        .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        header {
            background: var(--header-color);
            color: #fff;
            padding: 1rem 0;
            position: relative;
        }
        header .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        h1 {
            font-size: 2rem;
            font-weight: 500;
        }
        .logout {
            color: #fff;
            text-decoration: none;
            font-weight: 300;
            transition: opacity 0.3s ease;
        }
        .logout:hover {
            opacity: 0.8;
        }
        h2 {
            margin-bottom: 1rem;
            color: var(--primary-color);
        }
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 10px;
            margin-bottom: 2rem;
        }
        th, td {
            padding: 15px;
            text-align: left;
            background-color: #fff;
        }
        th {
            background-color: var(--primary-color);
            color: #fff;
            font-weight: 500;
        }
        tr {
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        tr:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: var(--secondary-color);
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .button:hover {
            background-color: #27ae60;
        }
        .edit-link {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        .edit-link:hover {
            color: #2980b9;
        }
        .status {
            font-weight: 300;
        }
        .status span {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 0.9em;
        }
        .status .pending {
            background-color: #f39c12;
            color: #fff;
        }
        .status .approved {
            background-color: #2ecc71;
            color: #fff;
        }
        @media (max-width: 768px) {
            table, thead, tbody, th, td, tr {
                display: block;
            }
            thead tr {
                position: absolute;
                top: -9999px;
                left: -9999px;
            }
            tr {
                margin-bottom: 15px;
            }
            td {
                border: none;
                position: relative;
                padding-left: 50%;
            }
            td:before {
                position: absolute;
                top: 6px;
                left: 6px;
                width: 45%;
                padding-right: 10px;
                white-space: nowrap;
                content: attr(data-label);
                font-weight: 500;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1>Dashboard นักเรียน</h1>
            <a href="?logout=1" class="logout">ออกจากระบบ</a>
        </div>
    </header>
    <div class="container">
        <h2>ประวัติการลา</h2>
        <?php
        if ($result->num_rows > 0) {
            echo "<table>
                    <thead>
                        <tr>
                            <th>ชื่อนักเรียน</th>
                            <th>ชั้นเรียน</th>
                            <th>วันที่เริ่ม</th>
                            <th>วันที่สิ้นสุด</th>
                            <th>เหตุผล</th>
                            <th>สถานะ</th>
                            <th>การดำเนินการ</th>
                        </tr>
                    </thead>
                    <tbody>";

            while ($row = $result->fetch_assoc()) {
                $status = '';
                $approval_levels = ['ครูประจำชั้น', 'หัวหน้าระดับ', 'รองผู้อำนวยการบริหารงานกิจการนักเรียนและหอพัก', 'รองผู้อำนวยการบริหารงานวิชาการ'];
                foreach ($approval_levels as $index => $level) {
                    $approval_key = 'approval_level_' . ($index + 1);
                    $status_class = $row[$approval_key] ? 'approved' : 'pending';
                    $status_text = $row[$approval_key] ? 'อนุมัติ' : 'รอดำเนินการ';
                    $status .= "<div class='status'>{$level}: <span class='{$status_class}'>{$status_text}</span></div>";
                }

                echo "<tr>";
                echo "<td data-label='ชื่อนักเรียน'>" . htmlspecialchars($row['prefix']) . " " . htmlspecialchars($row['first_name']) . " " . htmlspecialchars($row['last_name']) . "</td>";
                echo "<td data-label='ชั้นเรียน'>" . htmlspecialchars($row['class']) . "</td>";
                echo "<td data-label='วันที่เริ่ม'>" . htmlspecialchars($row['start_date']) . "</td>";
                echo "<td data-label='วันที่สิ้นสุด'>" . htmlspecialchars($row['end_date']) . "</td>";
                echo "<td data-label='เหตุผล'>" . htmlspecialchars($row['reason']) . "</td>";
                echo "<td data-label='สถานะ'>{$status}</td>";
                echo "<td data-label='การดำเนินการ'>";
                if ($row['approval_level_1'] === null) {
                    echo "<a href='edit_request.php?id=" . $row['id'] . "' class='edit-link'>แก้ไข</a>";
                } else {
                    echo "ไม่สามารถแก้ไขได้";
                }
                echo "</td>";
                echo "</tr>";
            }
            echo "</tbody></table>";
        } else {
            echo "<p>ไม่พบประวัติการลา</p>";
        }
        ?>
        <a href="request_form.php" class="button">ขอลาเพิ่ม</a>
    </div>
</body>
</html>

<?php
$conn->close();
?>