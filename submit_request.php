<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "leave_requests";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve form data
$student_id = $_POST['student_id'];
$prefix = $_POST['prefix'];
$first_name = $_POST['first_name'];
$last_name = $_POST['last_name'];
$class = $_POST['class'];
$student_number = $_POST['student_number'];
$start_date = $_POST['start_date'];
$end_date = $_POST['end_date'];
$reason = $_POST['reason'];

// Insert into database
$sql = "INSERT INTO requests (student_id, prefix, first_name, last_name, class, student_number, start_date, end_date, reason) 
        VALUES ('$student_id', '$prefix', '$first_name', '$last_name', '$class', '$student_number', '$start_date', '$end_date', '$reason')";

if ($conn->query($sql) === TRUE) {
    echo "New request submitted successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
