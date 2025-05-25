<?php
session_start();
include 'db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $new_pass = trim($_POST['new_password']);

    if (!empty($username) && !empty($new_pass)) {
        $hash = password_hash($new_pass, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("UPDATE accounts SET password_hash = ? WHERE username = ?");
        $stmt->bind_param("ss", $hash, $username);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $success = "✅ Password reset successfully!";
        } else {
            $error = "❌ Username not found or update failed.";
        }
    } else {
        $error = "❌ All fields are required.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Password - MUST</title>
    <style>
        body { font-family: Arial; background: #f5f5f5; }
        .box { width: 400px; margin: 100px auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px #ccc; }
        input, button { width: 100%; padding: 10px; margin-top: 10px; }
        .error { color: red; font-weight: bold; }
        .success { color: green; font-weight: bold; }
    </style>
</head>
<body>
<div class="box">
    <h2>🔑 Reset Your Password</h2>

    <?php if ($error): ?>
        <p class="error"><?= $error ?></p>
    <?php elseif ($success): ?>
        <p class="success"><?= $success ?></p>
        <p><a href="index.php">Login Now</a></p>
    <?php endif; ?>

    <form method="post">
        Username: <input type="text" name="username" required><br>
        New Password: <input type="password" name="new_password" required><br>
        <button type="submit">Reset Password</button>
    </form>

    <p><a href="index.php">← Back to Login</a></p>
</div>
</body>
</html>
