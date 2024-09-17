<?php
session_start();
require 'db_connect.php';

// Define allowed roles and set default
$allowed_roles = ['student', 'approver'];
$role = isset($_GET['role']) && in_array($_GET['role'], $allowed_roles) ? $_GET['role'] : 'student';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($role === 'student') {
        // SQL query to check user credentials from the 'users' table for students
        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            if ($password === $user['password']) {
                                // Set session for student
                                $_SESSION['username'] = $user['username'];
                                $_SESSION['role'] = 'student'; // Set the role as 'student'
                
                                // Redirect to student dashboard
                                header("Location: student_dashboard.php");
                                exit();
                            } else {
                                $error_message = "รหัสผ่านไม่ถูกต้องสำหรับนักเรียน";
                            }
                        } else {
                            $error_message = "ไม่พบนักเรียนที่ใช้ชื่อผู้ใช้นี้";
                        }
                
                    } elseif ($role === 'approver') {
                        // SQL query to check user credentials from the 'approvers' table for approvers
                        $sql = "SELECT * FROM approvers WHERE username = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("s", $username);
                        $stmt->execute();
                        $result = $stmt->get_result();
                
                        if ($result->num_rows == 1) {
                            $approver = $result->fetch_assoc();
                            if ($password === $approver['password']) {
                                // Set session for approver
                                $_SESSION['username'] = $approver['username'];
                                $_SESSION['role'] = 'approver'; // Set the role as 'approver'
                
                                // Redirect to approval page
                                header("Location: approval_page.php");
                                exit();
                            } else {
                                $error_message = "รหัสผ่านไม่ถูกต้องสำหรับผู้อนุมัติ";
                            }
                        } else {
                            $error_message = "ไม่พบผู้อนุมัติที่ใช้ชื่อผู้ใช้นี้";
                        }
                    }
                }
                ?>
                
                <!DOCTYPE html>
                <html lang="en">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Login - <?php echo htmlspecialchars(ucfirst($role)); ?></title>
                    <style>
                        body {
                            font-family: 'Arial', sans-serif;
                            background-color: #f0f2f5;
                            margin: 0;
                            padding: 0;
                            display: flex;
                            justify-content: center;
                            align-items: center;
                            min-height: 100vh;
                        }
                        .container {
                            background-color: #fff;
                            padding: 2rem;
                            border-radius: 10px;
                            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
                            width: 100%;
                            max-width: 400px;
                            text-align: center;
                        }
                        h1 {
                            font-size: 2rem;
                            margin-bottom: 1.5rem;
                            color: #007bff;
                        }
                        form {
                            display: flex;
                            flex-direction: column;
                            gap: 1.5rem;
                        }
                        label {
                            text-align: left;
                            color: #555;
                            font-weight: bold;
                        }
                        input[type="text"], input[type="password"] {
                            padding: 0.75rem;
                            border: 1px solid #ccc;
                            border-radius: 6px;
                            font-size: 1rem;
                            width: 100%;
                            transition: border-color 0.3s ease;
                        }
                        input[type="text"]:focus, input[type="password"]:focus {
                            border-color: #007bff;
                        }
                        input[type="submit"] {
                            background-color: #007bff;
                            color: #fff;
                            padding: 0.75rem;
                            border: none;
                            border-radius: 6px;
                            font-size: 1.1rem;
                            cursor: pointer;
                            transition: background-color 0.3s ease;
                        }
                        input[type="submit"]:hover {
                            background-color: #0056b3;
                        }
                        footer {
                            margin-top: 1.5rem;
                            color: #777;
                        }
                        .error-message {
                            color: #ff0000;
                            margin-bottom: 1rem;
                        }
                    </style>
                </head>
                <body>
                    <div class="container">
                        <h1>Login as <?php echo htmlspecialchars(ucfirst($role)); ?></h1>
                        <?php
                        if (isset($error_message)) {
                            echo "<p class='error-message'>" . htmlspecialchars($error_message) . "</p>";
                        }
                        ?>
                        <form action="login.php?role=<?php echo htmlspecialchars($role); ?>" method="post">
                            <label for="username">Username</label>
                            <input type="text" id="username" name="username" required placeholder="Enter your username">
                            
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" required placeholder="Enter your password">
                            
                            <input type="submit" value="Login">
                        </form>
                        <footer>
                            <p>&copy; 2024 Your Company</p>
                        </footer>
                    </div>
                </body>
                </html>
                
