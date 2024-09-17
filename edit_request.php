<?php
include 'db_connect.php';
session_start();

// ตรวจสอบการเข้าสู่ระบบ
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$request_id = $_GET['id'] ?? '';

if (!$request_id) {
    die("Invalid request ID");
}

// ดึงข้อมูลคำขอการลา
$sql = "SELECT * FROM leave_requests WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $request_id);
$stmt->execute();
$result = $stmt->get_result();
$request = $result->fetch_assoc();

if (!$request) {
    die("Request not found");
}

// ตรวจสอบการส่งฟอร์ม
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $document = $_FILES['document']['name']; // ชื่อไฟล์หลักฐานการลา

    // ตรวจสอบว่ามีการอัปโหลดไฟล์หรือไม่
    if ($_FILES['document']['error'] == UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';
        $upload_file = $upload_dir . basename($_FILES['document']['name']);
        if (move_uploaded_file($_FILES['document']['tmp_name'], $upload_file)) {
            // อัปเดตไฟล์หลักฐานการลาในฐานข้อมูล
            $sql = "UPDATE leave_requests SET document = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $document, $request_id);

            if ($stmt->execute()) {
                echo "<p class='success-message'>เอกสารหลักฐานการลาได้รับการอัปเดตเรียบร้อยแล้ว</p>";
            } else {
                echo "<p class='error-message'>เกิดข้อผิดพลาดในการอัปเดตเอกสาร: " . $conn->error . "</p>";
            }

            $stmt->close();
        } else {
            echo "<p class='error-message'>เกิดข้อผิดพลาดในการอัปโหลดไฟล์</p>";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขคำขอลา</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2ecc71;
            --background-color: #ecf0f1;
            --text-color: #34495e;
            --card-background: #fff;
            --border-color: #ddd;
        }
        body {
            font-family: 'Kanit', sans-serif;
            background-color: var(--background-color);
            color: var(--text-color);
            line-height: 1.6;
        }
        .container {
            width: 90%;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        header {
            background: var(--primary-color);
            color: #fff;
            padding: 1rem 0;
            text-align: center;
        }
        h1 {
            font-size: 2rem;
            margin: 0;
        }
        .form-container {
            background: var(--card-background);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        label {
            display: block;
            margin-bottom: 10px;
            font-weight: 500;
        }
        input[type="file"] {
            display: block;
            margin-bottom: 20px;
        }
        .button, .back-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: var(--secondary-color);
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .button:hover, .back-button:hover {
            background-color: #27ae60;
        }
        .back-button {
            background-color: var(--primary-color);
            margin-top: 20px;
        }
        .success-message, .error-message {
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .success-message {
            background-color: #2ecc71;
            color: #fff;
        }
        .error-message {
            background-color: #e74c3c;
            color: #fff;
        }
    </style>
</head>
<body>
    <header>
        <h1>แก้ไขเอกสาร</h1>
    </header>
    <div class="container">
        <div class="form-container">
            <form method="post" enctype="multipart/form-data">
                <label for="document">ไฟล์หลักฐานการลา (ปัจจุบัน: <?php echo htmlspecialchars($request['document']); ?>)</label>
                <input id="document" name="document" type="file" required>

                <input type="submit" value="อัปเดตเอกสาร" class="button">
            </form>
            <a href="student_dashboard.php" class="back-button">กลับสู่ Dashboard</a>
        </div>
    </div>
</body>
</html>

<?php
$conn->close();
?>
