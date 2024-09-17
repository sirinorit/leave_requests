<?php
session_start();
require 'db_connect.php'; // เชื่อมต่อฐานข้อมูล

// ดึงข้อมูลผู้ใช้จากฐานข้อมูล
$username = $_SESSION['username'];
$sql = "SELECT prefix, first_name, last_name, class FROM users WHERE username = '$username'";
$result = $conn->query($sql);
$user = $result->fetch_assoc();

// ตรวจสอบว่ามีข้อมูลผู้ใช้หรือไม่
if (!$user) {
    die("ไม่พบข้อมูลผู้ใช้");
}

// ตั้งค่าเริ่มต้นสำหรับคำนำหน้า
$prefix = $user['prefix'] ?? '';
$first_name = $user['first_name'] ?? '';
$last_name = $user['last_name'] ?? '';
$class = $user['class'] ?? '';

// ตรวจสอบการส่งฟอร์ม
$success_message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // ข้อมูลที่รับมาจากฟอร์ม
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $reason = $_POST['reason'];
    $leave_type = $_POST['leave_type']; // ประเภทการลา
    $half_day = $_POST['half_day'] ?? ''; // การลาแบบครึ่งวัน
    $document = $_FILES['document']['name']; // ชื่อไฟล์หลักฐานการลา

    // ตรวจสอบว่ามีการอัปโหลดไฟล์หรือไม่
    if ($_FILES['document']['error'] == UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';
        // ตรวจสอบว่าโฟลเดอร์ uploads มีอยู่หรือไม่
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        $upload_file = $upload_dir . basename($_FILES['document']['name']);
        if (!move_uploaded_file($_FILES['document']['tmp_name'], $upload_file)) {
            die("การอัปโหลดไฟล์ล้มเหลว");
        }
    }

    // คำสั่ง SQL สำหรับเพิ่มข้อมูลการขอลา โดยใช้ข้อมูลจากฟอร์ม
    $sql = "INSERT INTO leave_requests (username, start_date, end_date, reason, leave_type, half_day, document) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssss", $username, $start_date, $end_date, $reason, $leave_type, $half_day, $document);

    if ($stmt->execute()) 
    {
        $success_message = "บันทึกการขอลาสำเร็จ";
         // ถ้าการบันทึกสำเร็จ เปลี่ยนเส้นทางไปยังหน้า student_dashboard.php
         header("Location: student_dashboard.php");
         exit();
    } else {
        $success_message = "เกิดข้อผิดพลาดในการบันทึกการขอลา: " . $conn->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แบบฟอร์มขอลา</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f8fa;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .container {
            background-color: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 600px;
        }
        h1 {
            text-align: center;
            color: #1877f2;
            margin-bottom: 2rem;
            font-size: 1.8rem;
        }
        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1.5rem;
            text-align: center;
            font-size: 1.1rem;
            border: 1px solid #c3e6cb;
        }
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1.5rem;
            text-align: center;
            font-size: 1.1rem;
            border: 1px solid #f5c6cb;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin-bottom: 0.5rem;
            font-size: 1rem;
            color: #333;
        }
        input, textarea, select {
            padding: 0.8rem;
            margin-bottom: 1rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            width: 100%;
        }
        input[type="submit"] {
            background-color: #1877f2;
            color: white;
            padding: 0.75rem;
            border: none;
            border-radius: 5px;
            font-size: 1.1rem;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        input[type="submit"]:hover {
            background-color: #166fe5;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>แบบฟอร์มขอลา</h1>

        <?php if ($success_message): ?>
            <div class="<?php echo strpos($success_message, 'สำเร็จ') !== false ? 'success-message' : 'error-message'; ?>">
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data">
            <label for="prefix">คำนำหน้า</label>
            <input id="prefix" type="text" value="<?php echo htmlspecialchars($prefix); ?>" disabled>

            <label for="first_name">ชื่อ</label>
            <input id="first_name" type="text" value="<?php echo htmlspecialchars($first_name); ?>" disabled>

            <label for="last_name">นามสกุล</label>
            <input id="last_name" type="text" value="<?php echo htmlspecialchars($last_name); ?>" disabled>

            <label for="class">ชั้นเรียน</label>
            <input id="class" type="text" value="<?php echo htmlspecialchars($class); ?>" disabled>

            <label for="start_date">วันที่เริ่ม</label>
            <input id="start_date" name="start_date" type="date" required>

            <label for="end_date">วันที่สิ้นสุด</label>
            <input id="end_date" name="end_date" type="date" required>

            <label for="reason">เหตุผลในการลา</label>
            <textarea id="reason" name="reason" rows="4" required></textarea>

            <label for="leave_type">ประเภทการลา</label>
            <select id="leave_type" name="leave_type" required>
                <option value="">เลือกประเภทการลา</option>
                <option value="ลาป่วย">ลาป่วย</option>
                <option value="ลากิจ">ลากิจ</option>
            </select>

            <label for="half_day">ชนิดการลา</label>
            <select id="half_day" name="half_day">
                <option value="">เลือกชนิดการลา</option>
                <option value="เต็มวัน">ลาเต็มวัน</option>
                <option value="ครึ่งวันเช้า">ลาครึ่งวันเช้า</option>
                <option value="ครึ่งวันบ่าย">ลาครึ่งวันบ่าย</option>
            </select>

            <label for="document">ไฟล์หลักฐานการลา</label>
            <input id="document" name="document" type="file" required>

            <input type="submit" value="ส่งคำขอ">
        </form>
    </div>
</body>
</html>

<?php
$conn->close();
?>
