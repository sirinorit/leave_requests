<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Index - Choose Role</title>
    <style>
        /* CSS สำหรับการออกแบบ */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        header {
            width: 100%;
            background-color: #007bff;
            padding: 1rem 0;
            text-align: center;
            color: white;
            font-size: 1.5rem;
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
            color: #007bff;
            margin-bottom: 1.5rem;
        }
        a.button {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            margin: 0.5rem;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-size: 1.1rem;
            transition: background-color 0.3s ease;
        }
        a.button:hover {
            background-color: #0056b3;
        }
        footer {
            margin-top: 2rem;
            color: #777;
        }
    </style>
</head>
<body>
    <header>
    Leave Request System
    </header>
    <div class="container">
        <h1>Choose Your Role</h1>
        <a href="login.php?role=student" class="button">I am a Student</a>
        <a href="login.php?role=approver" class="button">I am an Approver</a>
    </div>
    <footer>
        <p>&copy; Princess Chulabhorn Science High Schools Pathum Thani</p>
    </footer>
</body>
</html>
