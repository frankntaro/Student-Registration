<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access denied.");
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        $message = "<p style='color:red;'>❌ Passwords do not match.</p>";
    } else {
        $stmt = $conn->prepare("SELECT id FROM accounts WHERE username = ? AND role = 'student'");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $hash = password_hash($new_password, PASSWORD_DEFAULT);

            $update = $conn->prepare("UPDATE accounts SET password_hash = ?, account_locked = 0 WHERE username = ?");
            $update->bind_param("ss", $hash, $username);

            if ($update->execute()) {
                $message = "<p style='color:green;'>✅ Password reset successfully and account unlocked.</p>";
            } else {
                $message = "<p style='color:red;'>❌ Failed to update password.</p>";
            }
        } else {
            $message = "<p style='color:red;'>❌ Student account not found.</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Student Password</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .container {
            margin: 40px auto;
            max-width: 500px;
            padding: 20px;
            background: white;
            border-radius: 8px;
        }

        input, button {
            width: 100%;
            padding: 10px;
            margin-top: 8px;
            font-size: 16px;
        }

        button {
            background: #004080;
            color: white;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background: #003060;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>🔐 Reset Student Password</h2>
        <?= $message ?>
        <form method="POST">
            Username (Student):  
            <input type="text" name="username" required>

            New Password:  
            <input type="password" name="new_password" required>

            Confirm Password:  
            <input type="password" name="confirm_password" required>

            <button type="submit">Reset Password</button>
        </form>
    </div>
</body>
</html>
