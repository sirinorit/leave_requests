<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "leave_requests";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$input_username = $_POST['username'];
$input_password = $_POST['password'];

// ตรวจสอบข้อมูลผู้ใช้
$sql = "SELECT * FROM users WHERE username = '$input_username' AND password = '".md5($input_password)."'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $_SESSION['username'] = $input_username;
    $_SESSION['role'] = $result->fetch_assoc()['role']; // ตรวจสอบบทบาทของผู้ใช้
    header("Location: index.php");
} else {
    echo "Invalid username or password";
}

$conn->close();
?>
