<?php
$password = 'Ntaro@must';
$hash = password_hash($password, PASSWORD_DEFAULT);
echo "Hashed Password: " . $hash;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Welcome to MUST Portal</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .box {
            background: white;
            padding: 40px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0px 0px 12px rgba(0,0,0,0.2);
        }
        .box h2 {
            margin-bottom: 20px;
            color: #333;
        }
        .box a {
            display: block;
            margin: 15px auto;
            padding: 10px 20px;
            width: 200px;
            background-color: #0077cc;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .box a.admin {
            background-color: #d63333;
        }
        .box a:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>
<div class="box">
    <h2>Welcome to MUST Registration Portal</h2>
    <p>Login As:</p>
    <a href="student_login.php">STUDENT</a>
    <a href="admin_login.php" class="admin">ADMIN</a>
</div>
</body>
</html>
